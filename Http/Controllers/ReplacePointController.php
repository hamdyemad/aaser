<?php

namespace App\Http\Controllers;

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
use App\Http\Resources\ReplacePointResource;
use App\Http\Resources\RewardRequestResource;
use App\Http\Requests\AddReplaceRewardRequestRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ReplacePointController extends Controller
{
    public function add(ReplacePointRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $randomEmail = 'provider' . Str::uuid() . '@asser-service-provider.com';
            $randomPasswordPlain = Str::random(16);
            $randomPasswordHashed = Hash::make($randomPasswordPlain);

            $file = $request->file ? $request->file('file')->store('replace') : null;
            $image = $request->image ? $request->file('image')->store('replace') : null;
            $replace_point = ReplacePoint::create([
                'reward_address' => $request->reward_address,
                'reward_description' => $request->reward_description,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
                'file' => $file,
                'image' => $image,
                'send_notification' => $request->send_notification,
                'have_count' => $request->have_count,
                'count_people' => $request->count_people,
            ]);

            $service_provider = ServiceProvider::create([
                'name' => 'مقدم خدمة ' . $replace_point->name,
                'email' => $randomEmail,
                'password' => $randomPasswordHashed,
                'unencrypted_password' => $randomPasswordPlain,
                'phone' => $request->phone[0] ?? null,
                'side' => $replace_point->location,
                'active' => 1,
                'total_points' => 0,
                'specialized_provider' => 1,
                'specialized_type' => 'replace-point',
                'specialized_id' => $replace_point->id,
            ]);

            if($request->provider_name)
            {
                $provider = ProviderReplace::create([
                    'replace_id' => $replace_point->id,
                    'name' => $request->provider_name,
                    'address' => $request->provider_address,
                    'website_url' => $request->provider_website,
                    'location' => $request->provider_location,
                    'num_hours' => $request->provider_num_hours,
                ]);
                if($request->provider_phone)
                {
                    foreach($request->provider_phone as $phone)
                    {
                        ProviderReplacePhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }

            if($request->send_notification == 1)
            {
                $users = User::all();
                foreach($users as $user)
                {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => 'يوجد مكافئة جديدة '. $replace_point->reward_address,
                        'page' => 'view-services',
                        'product_name' => $replace_point->reward_address,
                        'product_id' => $replace_point->id,
                    ]);
                }
            }
            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneReplacePoint::create([
                        'replace_point_id' => $replace_point->id,
                        'phone' => $phone,
                    ]);
                }
            }
            if($request->term)
            {
                foreach($request->term as $term)
                {
                    TermReplacePoint::create([
                        'replace_point_id' => $replace_point->id,
                        'term' => $term,
                    ]);
                }
            }
            if($request->name)
            {
                $reward_images = $request->reward_image ?? [];
                foreach($request->name as $key => $name)
                {
                    $reward_image = isset($reward_images[$key]) && $reward_images[$key]
                        ? $reward_images[$key]->store('stock')
                        : null;
                    RewardReplacePoint::create([
                        'replace_point_id' => $replace_point->id,
                        'name' => $name,
                        'point' => $request->point[$key],
                        'qty' => $request->qty[$key],
                        'available' => $request->available[$key],
                        'residual' => $request->qty[$key],
                        'appointment' => $request->appointment[$key],
                        'end_date' => $request->end_date[$key],
                        'image' => $reward_image,
                    ]);
                }
            }
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Replace Point Added Successfully',
        ]);
    }

    public function edit(ReplacePointRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $replace_point = ReplacePoint::findorFail($id);
            if($request->file)
            {
                if($replace_point->file)
                {
                    Storage::delete($replace_point->file);
                }
            }
            $file = $request->file ? $request->file('file')->store('replace') : $replace_point->file;
            if($request->image)
            {
                if($replace_point->image)
                {
                    Storage::delete($replace_point->image);
                }
            }
            $image = $request->image ? $request->file('image')->store('replace') : $replace_point->image;
            $replace_point->update([
                'reward_address' => $request->reward_address,
                'reward_description' => $request->reward_description,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
                'file' => $file,
                'image' => $image,
            ]);
            if($request->provider_name)
            {
                $replace_point->provider()->delete();
                $provider = ProviderReplace::create([
                    'replace_id' => $replace_point->id,
                    'name' => $request->provider_name,
                    'address' => $request->provider_address,
                    'website_url' => $request->provider_website,
                    'location' => $request->provider_location,
                    'num_hours' => $request->provider_num_hours,
                ]);
                if($request->provider_phone)
                {
                    foreach($request->provider_phone as $phone)
                    {
                        ProviderReplacePhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }
            if($request->phone)
            {
                $replace_point->phones()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneReplacePoint::create([
                        'replace_point_id' => $replace_point->id,
                        'phone' => $phone,
                    ]);
                }
            }
            if($request->term)
            {
                $replace_point->terms()->delete();
                foreach($request->term as $term)
                {
                    TermReplacePoint::create([
                        'replace_point_id' => $replace_point->id,
                        'term' => $term,
                    ]);
                }
            }
            if($request->name)
            {
                $replace_point->rewards()->delete();
                $reward_images = $request->reward_image ?? [];
                foreach($request->name as $key => $name)
                {
                    $reward_image = isset($reward_images[$key]) && $reward_images[$key]
                        ? $reward_images[$key]->store('stock')
                        : null;
                    RewardReplacePoint::create([
                        'replace_point_id' => $replace_point->id,
                        'name' => $name,
                        'point' => $request->point[$key],
                        'qty' => $request->qty[$key],
                        'available' => $request->available[$key],
                        'residual' => $request->qty[$key],
                        'appointment' => $request->appointment[$key],
                        'end_date' => $request->end_date[$key],
                        'image' => $reward_image,
                    ]);
                }
            }
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Replace Point Edited Successfully',
        ]);
    }

    public function delete($id)
    {
        $replace_point = ReplacePoint::findorFail($id);
        if($replace_point->file)
        {
            Storage::delete($replace_point->file);
        }
        if($replace_point->image)
        {
            Storage::delete($replace_point->image);
        }
        $replace_point->phones()->delete();
        $replace_point->terms()->delete();
        $replace_point->rewards()->delete();
        $replace_point->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Replace Point Deleted Successfully',
        ]);
    }


    public function show($id)
    {
        $replace_point = ReplacePoint::with('phones','terms','rewards','provider')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new ReplacePointResource($replace_point),
            'message' => 'Stock Point Retuned Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $replace_point = ReplacePoint::with('phones','terms','rewards','provider')->orderBy('id', 'desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => ReplacePointResource::collection($replace_point),
            'message' => 'Stock Points Retuned Successfully',
            'pagination' => [
                'current_page' => $replace_point->currentPage(),
                'last_page' => $replace_point->lastPage(),
                'per_page' => $replace_point->perPage(),
                'total' => $replace_point->total(),
            ],
        ]);
    }

public function addReplaceRewardRequest(AddReplaceRewardRequestRequest $request)
{

    $validator = Validator::make($request->all(), [
        'reward' => ['array', 'required'],
        'reward.*' => ['required'],
        'qty' => ['array', 'required'],
        'qty.*' => ['required', 'integer'],
        'date' => ['array', 'required'],
        'date.*' => ['required', 'date', 'date_format:Y-m-d'],
    ]);

    $validator->after(function ($validator) use ($request) {
        $rewardIds = $request->input('reward', []);
        $qty = $request->input('qty', []);
        $dates = $request->input('date', []);


        if(count($rewardIds) > 0) {
            foreach ($rewardIds as $index => $rewardId) {
                $reward_replace_point = RewardReplacePoint::find($rewardId);

                $inserted_date =  Carbon::parse($dates[$index]);


                if($reward_replace_point->available < $qty[$index]) {
                    $validator->errors()->add("qty.$index", "the qty is bigger than available rewards");
                }

                if($reward_replace_point->available < 1) {
                    $validator->errors()->add("reward.$index", "the reward is not available");
                }
                $reward_replace_point_end_date =  Carbon::parse($reward_replace_point->end_date);

                if($reward_replace_point_end_date->lt($inserted_date)) {
                    $validator->errors()->add("date", "the reward date invalid please check it again");
                }

            }
        }

    });

    if($validator->fails()) {
        return response()->json([
            'status' => 'Fail',
            'data' => [],
            'message' => $validator->errors()->first(),
        ], 422);
    }

    DB::beginTransaction();

    try {
        // التحقق من تسجيل الدخول كمستخدم
        if (!auth('user')->check()) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'You Should Be User',
            ], 422);
        }

        $auth = auth('user')->user(); // الحصول على المستخدم المُسجل
        $total_reward_points = 0;

        // حساب إجمالي النقاط المطلوبة والتحقق من الكميات
        foreach ($request->reward as $key => $rewardId) {
            $reward_point = RewardReplacePoint::findOrFail($rewardId);

            if (
                $request->qty[$key] > $reward_point->available ||
                $request->qty[$key] > $reward_point->residual
            ) {
                return response()->json([
                    'status' => 'Fail',
                    'data' => [],
                    'message' => 'No Quantity Available',
                ]);
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
            $request_id = rand(1000, 9999);

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
                ]);

                TrackPoint::create([
                    'point_id' => $point->id,
                    'point' => -($reward_point->point * $request->qty[$key]),
                    'comment' => 'خصم ' . ($reward_point->point * $request->qty[$key]) . ' نقاط بسبب التقديم على استبدال المكافأة',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'Success',
                'data' => new RewardRequestResource($reward_request),
                'message' => 'Reward Request Added Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'You Do Not Have Enough Points',
            ], 422);
        }
    } catch (\Exception $exception) {
        DB::rollBack();

        return response()->json([
            'status' => 'Fail',
            'data' => [],
            'message' => $exception->getMessage(),
        ], 500);
    }
}

}
