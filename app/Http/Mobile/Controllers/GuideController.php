<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guide;
use App\Models\FileGuide;
use App\Models\GuideTerm;
use App\Models\GuideType;
use App\Models\GuideOffer;
use App\Models\ImageGuide;
use App\Models\PhoneGuide;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ProviderGuide;
use App\Models\ProviderGuidePhone;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\GuideRequest;
use App\Http\Resources\GuideResource;
use App\Http\Requests\EditGuideRequest;
use App\Http\Resources\AdResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\GuideTypeResource;
use App\Http\Resources\RewardRequestResource;
use App\Models\Ad;
use App\Models\RequestGuide;
use App\Models\RewardRequest;
use App\Models\ServiceProvider;
use App\Services\GeneratePDFService;
use App\Traits\Res;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Mpdf\Mpdf;

class GuideController extends Controller
{
    use Res;

    public function __construct(public GeneratePDFService $generatePDFService)
    {
    }

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $page = $request->page ?? 1;
        $guides = Guide::with('type', 'offers','phone','image','file')->latest();
        $guides_types = GuideType::all();
        $type_id = $request->type_id ?? '';
        if($type_id) {
            $guides = $guides->where('type_id', $type_id);
        }
        $guides = $guides->paginate($per_page);
        $guides_types = GuideTypeResource::collection($guides_types);
        $guides = GuideResource::collection($guides);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'guide');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => [],
            'guides_types' => $guides_types,
            'guides' => $guides,
        ];

        if($page <= 1) {
            $data['ads'] = $ads;
        }

        return $this->sendRes('All Guide Return Successfully', true, $data, [], 200);
    }


    public function show($id)
    {
        $Guide = Guide::with('type', 'offers', 'terms', 'phone', 'image', 'file', 'provider')->findorFail($id);
        return $this->sendRes('Guide Return Successfully', true, new GuideResource($Guide), [], 200);
    }

     public function addOfferRequest(Request $request)
    {
        $auth = $request->user();

        $validator = Validator::make($request->all(), [
            'offer' => ['array', 'required'],
            'offer.*' => ['required'],
        ]);

        $validator->after(function ($validator) use ($request, $auth) {
            $offerIds = $request->input('offer', []);
            foreach ($offerIds as $index => $offerId) {
                $num_customers = RequestGuide::where('offer_id', $offerId)->count();
                $user_rewards = RewardRequest::where('user_id', $auth->id)->pluck('id')->toArray();
                $num_every_customer = RequestGuide::where('offer_id', $offerId)->whereIn('request_id', $user_rewards)->count();
                $offer_pount = GuideOffer::findorFail($offerId);

                $offer_end_date =  Carbon::parse($offer_pount->date);
                if($offer_end_date->lt(Carbon::now())) {
                    $validator->errors()->add("offer.$index", "the offer has reached the last date");
                }

                if($num_customers >= $offer_pount->num_customers) {
                    $validator->errors()->add("offer.$index", "the offer has reached the limit of requests");
                }
                if($num_every_customer >= $offer_pount->num_every_customer) {
                    $validator->errors()->add("offer.$index", "the offer has reached the maximum creation of request for each user");
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

            $request_id = rand(1000,9999);
            $reward_request = RewardRequest::create([
                'user_id' => $auth->id,
                'request_id' => $request_id,
            ]);

            foreach($request->offer as $offerId)
            {
                RequestGuide::create([
                    'request_id' => $reward_request->id,
                    'offer_id' => $offerId,
                ]);
            }
            $reward_request->load(['user', 'requestGuide.offer']);
            $pdf_data = [
                'reward_request' => new RewardRequestResource($reward_request),
            ];

            $pdf_response = $this->generatePDFService->genPDF($pdf_data, 'guide');
            $reward_request->update(['invoice' => $pdf_response['path']]);
            $response_data = [
                'pdf_url' => $pdf_response['full_path'],
            ];
            DB::commit();
            return $this->sendRes('Offer Request Added Successfully', true, $response_data, [], 200);

        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendRes($exception->getMessage(), false, [], [], 500);
        }
    }


}
