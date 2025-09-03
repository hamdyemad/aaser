<?php

namespace App\Http\Mobile\Controllers;

use App\Models\User;
use App\Models\Point;
use App\Models\TrackPoint;
use App\Models\Notification;
use App\Models\ReplacePoint;
use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\ProviderReplace;
use App\Models\TermReplacePoint;
use App\Models\PhoneReplacePoint;
use App\Models\RewardReplacePoint;
use Illuminate\Support\Facades\DB;
use App\Models\RequestReplacePoint;
use App\Http\Controllers\Controller;
use App\Models\ProviderReplacePhone;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ReplacePointRequest;
use App\Http\Resources\AdResource;
use App\Http\Resources\ReplacePointResource;
use App\Http\Resources\RewardRequestResource;
use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceProvider;
use App\Services\GeneratePDFService;
use App\Traits\Res;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ReplacePointController extends Controller
{
    use Res;

    public function __construct(public GeneratePDFService $generatePDFService)
    {
    }

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 12;
        $page = $request->page ?? 1;
        $replace_points = ReplacePoint::with('phones', 'terms', 'rewards', 'provider')->latest()->paginate($per_page);
        ReplacePointResource::collection($replace_points);


        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'replace_point');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => [],
            'replace_points' => $replace_points,
        ];

        if($page <= 1) {
            $data['ads'] = $ads;
        }


        return $this->sendRes('All Replace Points Return Successfully', true, $data, [], 200);
    }



    public function show($id)
    {
        $replace_point = ReplacePoint::with('phones', 'terms', 'rewards', 'provider')->find($id);
        if($replace_point) {
            return $this->sendRes('Replace Point Retuned Successfully', true, new ReplacePointResource($replace_point), [], 200);
        } else {
            return $this->sendRes('Replace Point not found', false, [], [], 404);
        }

    }



    public function addReplaceRewardRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reward' => ['array', 'required'],
            'reward.*' => ['required', 'exists:reward_replace_points,id'],
            'qty' => ['array', 'required'],
            'qty.*' => ['required', 'integer'],
            'date' => ['array', 'required'],
            'date.*' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $rewardIds = $request->input('reward', []);
            $qty = $request->input('qty', []);
            $dates = $request->input('date', []);


            if (count($rewardIds) > 0) {
                foreach ($rewardIds as $index => $rewardId) {
                    $reward_replace_point = RewardReplacePoint::find($rewardId);
                    if (!$reward_replace_point) {
                        $validator->errors()->add("reward.$index", "the reward is not found");
                    }

                    if ($reward_replace_point) {
                        $inserted_date =  Carbon::parse($dates[$index]);
                        if ($reward_replace_point->available < $qty[$index]) {
                            $validator->errors()->add("qty.$index", "the qty is bigger than available rewards");
                        }

                        if ($reward_replace_point->available < 1) {
                            $validator->errors()->add("reward.$index", "the reward is not available");
                        }
                        $reward_replace_point_end_date =  Carbon::parse($reward_replace_point->end_date);

                        if ($reward_replace_point_end_date->lt($inserted_date)) {
                            $validator->errors()->add("date", "the reward date invalid please check it again");
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
            $auth = auth('user')->user(); // الحصول على المستخدم المُسجل
            $total_reward_points = 0;

            // حساب إجمالي النقاط المطلوبة والتحقق من الكميات
            foreach ($request->reward as $key => $rewardId) {
                $reward_point = RewardReplacePoint::findOrFail($rewardId);

                if (
                    $request->qty[$key] > $reward_point->available ||
                    $request->qty[$key] > $reward_point->residual
                ) {
                    return $this->sendRes('No Quantity Available', false, [], [], 400);
                }

                $total_reward_points += $reward_point->point * $request->qty[$key];

                // تحديث الكمية المتبقية
                $reward_point->update([
                    'residual' => $reward_point->residual - $request->qty[$key],
                ]);
            }
            // الحصول على رصيد النقاط الحالي
            $point = Point::where('user_id', $auth->id)->first();

            if ($point && $point->points >= $total_reward_points) {
                $maxRequestId = RewardRequest::max('request_id');
                $request_id = $maxRequestId ? $maxRequestId + 1 : rand(1000, 9999);

                $reward_request = RewardRequest::create([
                    'user_id' => $auth->id,
                    'request_id' => $request_id,
                ]);

                // إنشاء تفاصيل الطلب وتتبع النقاط
                foreach ($request->reward as $key => $rewardId) {
                    $reward_point = RewardReplacePoint::findOrFail($rewardId);
                    $reward_point->update([
                        'available' => $reward_point->available - $request->qty[$key],
                    ]);
                    RequestReplacePoint::create([
                        'request_id' => $reward_request->id,
                        'replace_reward_id' => $rewardId,
                        'products_count' => $request->qty[$key],
                        'replace_date' => Carbon::parse($request->date[$key]),
                    ]);

                    TrackPoint::create([
                        'point_id' => $point->id,
                        'point' => - ($reward_point->point * $request->qty[$key]),
                        'comment' => 'خصم ' . ($reward_point->point * $request->qty[$key]) . ' نقاط بسبب التقديم على استبدال المكافأة',
                    ]);
                }

                $reward_request->load(['user', 'requestReplacePoint.replaceReward']);
                $pdf_data = [
                    'reward_request' => new RewardRequestResource($reward_request),
                ];

                $pdf_response = $this->generatePDFService->genPDF($pdf_data, 'request_replace_points');
                $reward_request->update(['invoice' => $pdf_response['path']]);
                $response_data = [
                    'pdf_url' => $pdf_response['full_path'],
                ];
                DB::commit();
                return $this->sendRes('Reward Request Added Successfully', true, $response_data, [], 200);

            } else {
                return $this->sendRes('You Do Not Have Enough Points', false, [], [], 422);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendRes($exception->getMessage(), false, [], [], 500);

        }
    }
}
