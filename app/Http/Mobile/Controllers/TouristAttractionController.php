<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Point;
use App\Models\TrackPoint;
use App\Models\Notification;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\RequestTouriste;
use App\Models\ProviderTouriste;
use App\Models\TouristAttraction;
use Illuminate\Support\Facades\DB;
use App\Models\FileTouristAttraction;
use App\Models\ProviderTouristePhone;
use App\Models\TermTouristAttraction;
use App\Models\ImageTouristAttraction;
use App\Models\PhoneTouristAttraction;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceTouristAttraction;
use App\Http\Resources\RewardRequestResource;
use App\Http\Requests\TouristAttractionRequest;
use App\Http\Resources\TouristAttractionResource;
use App\Http\Requests\EditTouristAttractionRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\TouristeAttractionRate;
use App\Services\GeneratePDFService;
use App\Traits\Res;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;

class TouristAttractionController extends Controller
{
    use Res;

    public function __construct(public GeneratePDFService $generatePDFService)
    {
    }

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $page = $request->page ?? 1;

        $tourist_attractions = TouristAttraction::with('phone', 'service', 'image', 'file')
            ->latest()
            ->paginate($per_page);
        TouristAttractionResource::collection($tourist_attractions);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'tourist_attraction');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => [],
            'tourist_attractions' => $tourist_attractions,
        ];

        if($page <= 1) {
            $data['ads'] = $ads;
        }


        return $this->sendRes('All Tourist Attractions Return Successfully', true, $data, [], 200);
    }


    public function show($id)
    {
        $tourist_attraction = TouristAttraction::with('phone', 'service', 'term', 'image', 'file', 'provider')->findorFail($id);
        $tourist_attraction = new TouristAttractionResource($tourist_attraction);
        return $this->sendRes('Tourist Attraction Return Successfully', true, $tourist_attraction, [], 200);
    }


    public function add_rate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|min:1|max:5',
            'tourist_attraction_id' => ['required', 'exists:tourist_attractions,id'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all(); // returns all error messages as an array
            $combinedMessage = implode('\n', $errors); // join all messages in one line
            return $this->sendRes($combinedMessage, false, [], $validator->errors(), 422);
        }


        $tourist_attraction = TouristAttraction::findorFail($request->tourist_attraction_id);
        $tourist_attraction_rate = TouristeAttractionRate::create([
            'tourist_attraction_id' => $tourist_attraction->id,
            'rate' => $request->rate,
        ]);

        return $this->sendRes('Rate Added Successfully', true, [], [], 200);
    }



    public function addTouristService(Request $request)
    {

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'service' => 'required|array',
                'service.*' => ['required', 'exists:service_tourist_attractions,id'],
                'qty' => 'required|array',
                'qty.*' => ['required', 'integer', 'min:1'],
                'date' => 'required|array',
                'date.*' => ['required', 'date_format:Y-m-d'],
            ]);

            $validator->after(function ($validator) use ($request) {
                $serviceIds = $request->input('service', []);
                $quantities = $request->input('qty', []);
                $dates = $request->input('date', []);
                foreach ($serviceIds as $index => $serviceId) {
                    $service_tourist_attraction = ServiceTouristAttraction::find($serviceId);
                    if (!$service_tourist_attraction) {
                        $validator->errors()->add("service.$index", "The service not found");
                    }
                    $qty = $quantities[$index] ?? 0;
                    $available = $service_tourist_attraction->available_count;

                    if ($available !== null && $qty > $available) {
                        $validator->errors()->add("qty.$index", "The quantity for service ID $serviceId exceeds available stock ($available).");
                    }
                    if (!empty($dates)) {
                        $service_date_incoming = Carbon::parse($dates[$index]);
                        $service_end_date =  Carbon::parse($service_tourist_attraction->date);
                        if ($service_end_date->lt($service_date_incoming)) {
                            $validator->errors()->add("date", "the date of service has reached the end date");
                        }
                    }
                }
            });



            if ($validator->fails()) {
                $errors = $validator->errors()->all(); // returns all error messages as an array
                $combinedMessage = implode('\n', $errors); // join all messages in one line
                return $this->sendRes($combinedMessage, false, [], $validator->errors(), 422);
            }

            $auth = $request->user();

            $maxRequestId = RewardRequest::max('request_id');
            $request_id = $maxRequestId ? $maxRequestId + 1 : rand(1000, 9999);

            $reward_request = RewardRequest::create([
                'user_id' => $auth->id,
                'request_id' => $request_id,
            ]);

            foreach ($request->service as $key => $serviceId) {
                $quantity = $request->qty[$key] ?? 1;
                $date = $request->date[$key] ?? 1;

                $touristService = ServiceTouristAttraction::find($serviceId);
                RequestTouriste::create([
                    'request_id' => $reward_request->id,
                    'service_tourist_attraction_id' => $serviceId,
                    'qty' => $quantity,
                    'date' => $date,
                ]);
                $touristService->available_count -= $quantity;
                $touristService->save();
            }

            $reward_request->load(['user', 'requestTouriste.serviceTouristAttraction']);

            $pdf_data = [
                'reward_request' => new RewardRequestResource($reward_request),
            ];
            $pdf_response = $this->generatePDFService->genPDF($pdf_data, 'tourist_attraction');

            $reward_request->update(['invoice' => $pdf_response['path']]);
            $response_data = [
                'pdf_url' => $pdf_response['full_path'],
            ];
            DB::commit();
            return $this->sendRes('Service Request Added Successfully', true, $response_data, [], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendRes($exception->getMessage(), false, [], [], 500);
        }
    }
}
