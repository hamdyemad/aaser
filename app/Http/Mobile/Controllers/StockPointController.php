<?php

namespace App\Http\Mobile\Controllers;

use App\Models\User;
use App\Models\Point;
use App\Models\StockPoint;
use App\Models\TrackPoint;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ProviderStock;
use App\Models\RewardRequest;
use App\Models\FileStockPoint;
use App\Models\TermStockPoint;
use App\Models\ImageStockPoint;
use App\Models\PhoneStockPoint;
use App\Models\RequestStockPoint;
use App\Models\ServiceStockPoint;
use App\Models\ProviderStockPhone;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StockPointRequest;
use App\Http\Resources\StockPointResource;
use App\Http\Resources\RewardRequestResource;
use App\Http\Requests\AddServiceRequestRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceProvider;
use App\Services\GeneratePDFService;
use App\Traits\Res;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Mpdf\Mpdf;

class StockPointController extends Controller
{
    use Res;


    public function __construct(public GeneratePDFService $generatePDFService)
    {
    }


    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $page = $request->page ?? 1;
        $stock_points = StockPoint::with('phones','terms','services','image','file','provider')->latest()->paginate($per_page);
        StockPointResource::collection($stock_points);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'stoke_points');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => [],
            'stock_points' => $stock_points,
        ];

        if($page <= 1) {
            $data['ads'] = $ads;
        }

        return $this->sendRes('Stock Points Retuned Successfully', true, $data, [], 200);
    }


    public function show($id)
    {
        $stock_point = StockPoint::with('phones','terms','services','image','file','provider')->findorFail($id);
        if($stock_point) {
            return $this->sendRes('Stock Point Return Successfully', true, new StockPointResource($stock_point), [], 200);
        } else {
            return $this->sendRes('Stock Point not found', false, [], [], 404);
        }
    }



    public function addServiceRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => ['array', 'required'],
            'service.*' => ['required', 'exists:service_stock_points,id'],
            'qty' => ['array', 'required'],
            'qty.*' => ['integer', 'min:1'],
            'date' => ['array', 'required'],
            'date.*' => ['required', 'date', 'date_format:Y-m-d']
        ]);

        $validator->after(function ($validator) use ($request) {
            $serviceIds = $request->input('service', []);
            $qty = $request->input('qty', []);
            $dates = $request->input('date', []);

            foreach ($serviceIds as $index => $serviceId) {
                $serviceStockPoint = ServiceStockPoint::find($serviceId);
                $serviceStockPoint_end_date =  Carbon::parse($serviceStockPoint->date);
                $serviceStockPoint_start_date =  Carbon::parse($serviceStockPoint->appointment);

                $inserted_date =  Carbon::parse($dates[$index]);


                if($serviceStockPoint_end_date->lte($inserted_date)) {
                    $validator->errors()->add("date", "that last date less than the inserted date");
                }

                if($serviceStockPoint_start_date->gte($inserted_date)) {
                    $validator->errors()->add("date", "that first date bigger than the inserted date");
                }

                if(isset($qty[$index])) {
                    if($serviceStockPoint->available_count < $qty[$index]) {
                        $validator->errors()->add("qty", "the available count of service stock point has reach the maximum");
                    }
                }

                if($serviceStockPoint->available_count < 1) {
                    $validator->errors()->add("qty", "the available count of service stock point has reach the maximum");
                }

            }
        });

        if ($validator->fails()) {
            $errors = $validator->errors()->all(); // returns all error messages as an array
            $combinedMessage = implode('\n', $errors); // join all messages in one line
            return $this->sendRes($combinedMessage, false, [], $validator->errors(), 422);
        }


        DB::beginTransaction();
        try
        {
            $auth = $request->user();

            $maxRequestId = RewardRequest::max('request_id');
            $request_id = $maxRequestId ? $maxRequestId + 1 : rand(1000, 9999);
            $reward_request = RewardRequest::create([
                'user_id' => $auth->id,
                'request_id' => $request_id,
            ]);
            foreach($request->service as $key => $serviceId)
            {
                $count = $request->qty[$key];
                $serviceStockPoint = ServiceStockPoint::find($serviceId);

                RequestStockPoint::create([
                    'request_id' => $reward_request->id,
                    'service_id' => $serviceId,
                    'products_count' => $count
                ]);
                $serviceStockPoint->available_count -= $count;
                $serviceStockPoint->save();
            }
            DB::commit();
            $reward_request->load(['user', 'requestStockPoint.service']);
            $pdf_data = [
                'reward_request' => $reward_request,
            ];

            $pdf_response = $this->generatePDFService->genPDF($pdf_data, 'request_stock_points');

            $reward_request->update(['invoice' => $pdf_response['path']]);
            $response_data = [
                'pdf_url' => $pdf_response['full_path'],
            ];

            return $this->sendRes('Service Request Added Successfully', true, $response_data, [], 200);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendRes($exception->getMessage(), false, [], [], 500);
        }
    }
}
