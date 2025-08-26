<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\RequestActivity;
use App\Models\ProviderActivitie;
use Illuminate\Support\Facades\DB;
use App\Models\EntertainmentActivity;
use App\Models\FileTouristAttraction;
use App\Models\TermTouristAttraction;
use App\Models\ImageTouristAttraction;
use App\Models\ProviderActivitiePhone;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceTouristAttraction;
use App\Models\FileEntertainmentActivity;
use App\Models\TermEntertainmentActivity;
use App\Models\ImageEntertainmentActivity;
use App\Models\PhoneEntertainmentActivity;
use App\Models\ServiceEntertainmentActivity;
use App\Http\Resources\RewardRequestResource;
use App\Http\Requests\EntertainmentActivityRequest;
use App\Http\Resources\EntertainmentActivityResource;
use App\Http\Requests\EditEntertainmentActivityRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Carbon\Carbon;
use App\Models\ServiceProvider;
use App\Services\GeneratePDFService;
use App\Traits\Res;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Mpdf\Mpdf;

class EntertainmentActivityController extends Controller
{
    use Res;

    public function __construct(public GeneratePDFService $generatePDFService)
    {
    }

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;

        $entertainment_activities = EntertainmentActivity::with('phone', 'file', 'image')
            ->latest()->paginate($per_page);
        EntertainmentActivityResource::collection($entertainment_activities);


        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'entertainment_activity');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => $ads,
            'entertainment_activities' => $entertainment_activities,
        ];

        return $this->sendRes('All Entertainment Activity Return Successfully', true, $data, [], 200);
    }

    public function show($id)
    {
        $EntertainmentActivity = EntertainmentActivity::with('phone', 'term', 'file', 'image', 'service', 'provider')->find($id);
        if ($EntertainmentActivity) {
            return $this->sendRes('Entertainment Activity Return Successfully', true, new EntertainmentActivityResource($EntertainmentActivity), [], 200);
        } else {
            return $this->sendRes('All Entertainment Activity not found', false, [], [], 404);
        }
    }


    public function activityService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => ['array', 'required'],
            'service.*' => ['required', 'exists:service_entertainment_activities,id'],
            'qty' => ['array', 'required'],
            'qty.*' => ['required', 'integer'],
            'date' => 'required|array',
            'date.*' => ['required', 'date_format:Y-m-d'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $serviceIds = $request->input('service', []);
            $qty = $request->input('qty', []);
            $dates = $request->input('date', []);

            if (count($serviceIds) > 0) {
                foreach ($serviceIds as $index => $serviceId) {
                    $service = ServiceEntertainmentActivity::find($serviceId);
                    if ($service) {
                        $service_from =  Carbon::parse($service->from);
                        if ($service->available_num_tickets < $qty[$index]) {
                            $validator->errors()->add("qty.$index", "the qty is bigger than available tickets");
                        }

                        if ($service->available_num_tickets < 1) {
                            $validator->errors()->add("service.$index", "the service of entertainment is not available");
                        }

                        $service_date_incoming = Carbon::parse($dates[$index]);
                        $service_to =  Carbon::parse($service->to);
                        if (!($service_from->lte($service_date_incoming) && $service_to->gte($service_date_incoming))) {
                            $validator->errors()->add("date", "the service of entertainment date invalid please check it again");
                        }
                    }
                }
            }
        });


        if ($validator->fails()) {
            $errors = $validator->errors()->all(); // returns all error messages as an array
            $combinedMessage = implode('\n', $errors); // join all messages in one line
            return $this->sendRes($combinedMessage, false, [], $validator->errors(), 422);
        }


        DB::beginTransaction();

        try {
            $auth = $request->user();
            $request_id = rand(1000, 9999);

            $reward_request = RewardRequest::create([
                'user_id' => $auth->id,
                'request_id' => $request_id,
            ]);

            $services = $request->input('service'); // service[]
            $qtys = $request->input('qty');
            $dates = $request->input('date');

            if (!is_array($services) || !is_array($qtys) || count($services) !== count($qtys)) {
                return $this->sendRes('Invalid service or qty format', false, [], [], 422);
            }

            foreach ($services as $index => $service_id) {
                $service = ServiceEntertainmentActivity::find($service_id);
                RequestActivity::create([
                    'request_id' => $reward_request->id,
                    'service_activity_id' => $service_id,
                    'qty' => $qtys[$index],
                    'date' => $dates[$index],
                ]);
                $service->available_num_tickets -= $qtys[$index];
                $service->save();
            }

            $reward_request->load(['user', 'requestActivity.serviceActivity']);
            $pdf_data = [
                'reward_request' => new RewardRequestResource($reward_request),
            ];

            $pdf_response = $this->generatePDFService->genPDF($pdf_data, 'entertainment_activity');
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
