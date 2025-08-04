<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Point;
use App\Models\Reward;
use App\Models\GuideOffer;
use App\Models\RewardTerm;
use App\Models\TrackPoint;
use App\Models\Notification;
use App\Models\RequestGuide;
use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\RequestActivity;
use App\Models\RequestTouriste;
use App\Models\ServiceProvider;
use App\Models\RequestStockPoint;
use Illuminate\Support\Facades\DB;
use App\Models\RequestReplacePoint;
use App\Http\Resources\RewardResource;
use App\Http\Requests\AddRewardRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditRewardRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AddOfferRequestRequest;
use App\Http\Resources\RewardRequestResource;
use App\Http\Requests\AddRewardRequestRequest;
use App\Models\ServiceEntertainmentActivity;
use App\Models\ServiceTouristAttraction;

class RewardController extends Controller
{
    public function addReward(AddRewardRequest $request)
    {
        $image = $request->file('image')->store('reward');
        $reward = Reward::create([
            'title' => $request->title,
            'location' => $request->location,
            'description' => $request->description,
            'image' => $image,
            'points' => $request->points,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'send_notification' => $request->send_notification,
            'have_count' => $request->have_count,
            'count_people' => $request->count_people,
        ]);

        if($request->send_notification == 1)
        {
            $users = User::all();
            foreach($users as $user)
            {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => 'You Have New Reward '. $reward->title,
                ]);
            }
        }

        if($request->terms)
        {
            foreach($request->terms as $term)
            {
                RewardTerm::create([
                    'reward_id' => $reward->id,
                    'title' => $term,
                ]);
            }
        }

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Reward Added Successfully',
        ]);
    }

    public function editReward(EditRewardRequest $request, $id)
    {
        $reward = Reward::findorFail($id);
        if($request->file('image'))
        {
            Storage::delete($reward->image);
        }
        $image = $request->file('image') ? $request->file('image')->store('reward') : $reward->image;
        $reward->update([
            'title' => $request->title,
            'location' => $request->location,
            'description' => $request->description,
            'image' => $image,
            'points' => $request->points,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        if($request->terms)
        {
            $reward->terms()->delete();
            foreach($request->terms as $term)
            {
                RewardTerm::create([
                    'reward_id' => $reward->id,
                    'title' => $term,
                ]);
            }
        }

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Reward Edited Successfully',
        ]);
    }

    public function deleteReward($id)
    {
        $reward = Reward::findorFail($id);
        Storage::delete($reward->image);
        $reward->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Reward Deleted Successfully',
        ]);
    }

    public function showReward($id)
    {
        $reward = Reward::with('requests','terms')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new RewardResource($reward),
            'message' => 'Reward Returned Successfully',
        ]);
    }

    public function allReward(Request $request)
    {
        $item = $request->item ?? 20;
        $rewards = Reward::with('requests','terms')->latest()->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => RewardResource::collection($rewards),
            'message' => 'Rewards Returned Successfully',
            'pagination' => [
                'current_page' => $rewards->currentPage(),
                'last_page' => $rewards->lastPage(),
                'per_page' => $rewards->perPage(),
                'total' => $rewards->total(),
            ],
        ]);
    }

    public function addRewardRequest(AddRewardRequestRequest $request)
    {
        $auth = $request->user();
        if(auth('user')->check())
        {
            $reward = Reward::findorFail($request->reward_id);
            if($reward->have_count == 1)
            {
                $count = RewardRequest::where('reward_id', $request->reward_id)->count();
                if($count >= $reward->count_people)
                {
                    return response()->json([
                        'status' => 'Fail',
                        'data' => [],
                        'message' => 'Sorry, The Number Completed With People',
                    ], 422);
                }
            }

            $point = Point::where('user_id',$auth->id)->first();
            if($point && $point->points >= $reward->points)
            {
                $request_id = rand(1000,9999);
                $reward_request = RewardRequest::create([
                    'reward_id' => $request->reward_id,
                    'user_id' => $auth->id,
                    'request_id' => $request_id,
                ]);

                $track_point = TrackPoint::create([
                    'point_id' => $point->id,
                    'point' => - $reward->points,
                    'comment' => 'Discount '. $reward->points . ' نقاط بسبب التقديم على مكافاه',
                ]);

                return response()->json([
                    'status' => 'Success',
                    'data' => new RewardRequestResource(RewardRequest::with('reward','user')->findorFail($reward_request->id)),
                    'message' => 'Reward Request Returned Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status' => 'Fail',
                    'data' => [],
                    'message' => 'You Do Not Have Enough Points',
                ], 422);
            }
        }

        return response()->json([
            'status' => 'Fail',
            'data' => [],
            'message' => 'You Should Be User',
        ], 422);
    }

    public function addOfferRequest(AddOfferRequestRequest $request)
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

        if($validator->fails()) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();
        try
        {
            if(!auth('user')->check())
            {
                return response()->json([
                    'status' => 'Fail',
                    'data' => [],
                    'message' => 'You Should Be User',
                ], 422);
            }

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

            DB::commit();
            return response()->json([
                'status' => 'Success',
                'data' => new RewardRequestResource($reward_request),
                'message' => 'Offer Request Added Successfully',
            ]);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'Fail',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function allRewardRequest(Request $request)
    {

        // $service_providers =
        $show_by_service_provider_id = request('show_by_service_provider_id');

        $requests = RewardRequest::with(
        'reward','user','provider',
        'requestGuide.offer',
        'requestStockPoint','requestReplacePoint'
        ,
        'requestTouriste'

        ,'requestActivity','visitorExhibitionConference','participantExhibitionConference'
        )
        ->when($request->filled('reward_id'), function($query) use($request){
            return $query->where('reward_id', $request->reward_id);
        })
        ->when($request->filled('id'), function($query) use($request){
            return $query->where('id', $request->id);
        })
        ->when($request->filled('visitor_exhibition_conference_id'), function($query) use($request){
            return $query->where('visitor_exhibition_conference_id', $request->visitor_exhibition_conference_id);
        })
        ->when($request->filled('participant_exhibition_conference_id'), function($query) use($request){
            return $query->where('participant_exhibition_conference_id', $request->participant_exhibition_conference_id);
        })
        ->when($request->filled('user_id'), function($query) use($request){
            return $query->where('user_id', $request->user_id);
        })
        ->when($request->filled('request_id'), function($query) use($request){
            return $query->where('request_id', $request->request_id);
        })
        ->when($request->filled('service_provider'), function($query) use($request){
            return $query->where('done_by_service_provider', $request->service_provider);
        })

        ->when($request->filled('show_by_service_provider_id'), function($query) use($show_by_service_provider_id){
            return $query
            ->whereHas('requestTouriste.serviceTouristAttraction.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })
            ->orWhereHas('requestGuide.offer.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })
            ->orWhereHas('requestActivity.serviceActivity.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })
            ->orWhereHas('requestStockPoint.service.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })
            ->orWhereHas('requestReplacePoint.replaceReward.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })
            ->orWhereHas('visitorExhibitionConference.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })
            ->orWhereHas('participantExhibitionConference.specialized_provider',
                function ($q) use ($show_by_service_provider_id) {
                    $q->where('id', $show_by_service_provider_id);
                })

                ;
        })
        ->latest();

        $requests = $requests->get();
        $requests = RewardRequestResource::collection($requests);
        return response()->json([
            'status' => 'Success',
            'data' => $requests,
            'message' => 'Reward Requests Returned Successfully',
        ]);
    }

    public function doneRequest(Request $request, $id)
    {
        $auth = auth('serviceProvider')->user();
        if(auth('serviceProvider')->check())
        {
            $reward_request = RewardRequest::findorFail($id);
            if($reward_request)
            {
                if($reward_request->status == 'done')
                {
                    return response()->json([
                        'status' => 'Fail',
                        'data' => [],
                        'message' => 'This Request Already Done Yet',
                    ], 422);
                }

                $reward_request->update([
                    'status' => 'done',
                    'done_date' => Carbon::now()->toDateTimeString(),
                    'done_by_service_provider' => $auth->id,
                ]);

                // if($reward_request->reward_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $the_service_provider->update([
                //         'total_points' => $the_service_provider->total_points + $reward_request->reward->points,
                //     ]);
                // }
                // elseif($reward_request->visitor_exhibition_conference_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $the_service_provider->update([
                //         'total_points' => $the_service_provider->total_points + $reward_request->visitorExhibitionConference->exhibition_conference->earn_points,
                //     ]);
                //     $point = Point::where('user_id',$reward_request->user_id)->first();
                //     $track_point = TrackPoint::create([
                //         'point_id' => $point->id,
                //         'point' => $reward_request->visitorExhibitionConference->exhibition_conference->earn_points * $reward_request->products_count,
                //         'comment' => 'اضافة '. $reward_request->visitorExhibitionConference->exhibition_conference->earn_points * $reward_request->products_count . ' نقاط بسبب التقديم على المعارض والموتمرات',
                //     ]);
                // }
                // elseif($reward_request->participant_exhibition_conference_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $the_service_provider->update([
                //         'total_points' => $the_service_provider->total_points + $reward_request->participantExhibitionConference->exhibition_conference->earn_points,
                //     ]);
                //     $point = Point::where('user_id',$reward_request->user_id)->first();
                //     $track_point = TrackPoint::create([
                //         'point_id' => $point->id,
                //         'point' => $reward_request->participantExhibitionConference->exhibition_conference->earn_points * $reward_request->products_count,
                //         'comment' => 'اضافة '. $reward_request->participantExhibitionConference->exhibition_conference->earn_points * $reward_request->products_count . ' نقاط بسبب التقديم على المعارض والموتمرات',
                //     ]);
                // }
                // elseif($reward_request->service_activity_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $activites = RequestActivity::where('request_id', $reward_request->id)->get();
                //     foreach($activites as $activite)
                //     {
                //         $the_service_provider->update([
                //             'total_points' => $the_service_provider->total_points + $activite->serviceActivity->earn_points,
                //         ]);
                //         $point = Point::where('user_id', $reward_request->user_id)->first();
                //         $track_point = TrackPoint::create([
                //             'point_id' => $point->id,
                //             'point' => $activite->serviceActivity->earn_points,
                //             'comment' => 'اضافة '. $activite->serviceActivity->earn_points . ' نقاط بسبب التقديم على خدمة من الانشطه الترفيهيه',
                //         ]);
                //     }
                // }
                // elseif($reward_request->service_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $services = RequestStockPoint::where('request_id', $reward_request->id)->get();
                //     foreach($services as $service)
                //     {
                //         $the_service_provider->update([
                //             'total_points' => $the_service_provider->total_points + ($service->service->point * $service->products_count),
                //         ]);
                //         $point = Point::where('user_id',$reward_request->user_id)->first();
                //         $track_point = TrackPoint::create([
                //             'point_id' => $point->id,
                //             'point' => $service->service->point * $service->products_count,
                //             'comment' => 'اضافة '. $service->service->point * $service->products_count . ' نقاط بسبب التقديم على خدمه',
                //         ]);
                //     }
                // }
                // elseif($reward_request->replace_reward_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $replaces = RequestReplacePoint::where('request_id', $reward_request->id)->get();
                //     foreach($replaces as $replace)
                //     {
                //         $the_service_provider->update([
                //             'total_points' => $the_service_provider->total_points + ($replace->replaceReward->point * $replace->products_count),
                //         ]);
                //     }
                // }
                // elseif($reward_request->service_tourist_attraction_id)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $tourists = RequestTouriste::where('request_id', $reward_request->id)->get();
                //     foreach($tourists as $tourist)
                //     {
                //         $the_service_provider->update([
                //             'total_points' => $the_service_provider->total_points + $tourist->serviceTouristAttraction->earn_points,
                //         ]);
                //         $point = Point::where('user_id', $reward_request->user_id)->first();
                //         $track_point = TrackPoint::create([
                //             'point_id' => $point->id,
                //             'point' => $tourist->serviceTouristAttraction->earn_points,
                //             'comment' => 'اضافة '. $tourist->serviceTouristAttraction->earn_points . ' نقاط بسبب التقديم على المعالم السياحيه',
                //         ]);
                //     }
                // }
                // elseif($reward_request->requestGuide)
                // {
                //     $the_service_provider = ServiceProvider::findorFail($auth->id);
                //     $offers = RequestGuide::where('request_id', $reward_request->id)->get();
                //     foreach($offers as $offer)
                //     {
                //         $the_service_provider->update([
                //             'total_points' => $the_service_provider->total_points + $offer->offer->points,
                //         ]);

                //         $point = Point::where('user_id', $reward_request->user_id)->first();
                //         $track_point = TrackPoint::create([
                //             'point_id' => $point->id,
                //             'point' => $offer->offer->points,
                //             'comment' => 'اضافة '. $offer->offer->points . ' نقاط بسبب التقديم على العروض فى دليلك',
                //         ]);
                //     }
                // }
                // else
                // {
                //     return response()->json([
                //         'status' => 'Fail',
                //         'message' => 'نوع الطلب غير معروف',
                //     ], 422);
                // }

                return response()->json([
                    'status' => 'Success',
                    'data' => [],
                    'message' => 'Done Request Successfully',
                ]);
            }
        }

        return response()->json([
            'status' => 'Fail',
            'data' => [],
            'message' => 'You Should Be Service Provider',
        ], 422);
    }

public function confirmRequest(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $reward_request = RewardRequest::findOrFail($id);

        if ($reward_request->status == 'done') {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'This Request Already Done Yet',
            ], 422);
        }

        $reward_request->update([
            'status' => 'done',
            'done_date' => Carbon::now()->toDateTimeString(),
            'done_by_service_provider' => 1
        ]);

        $the_service_provider = User::findOrFail(1); // عدل ID حسب مقدم الخدمة الحقيقي
        $point = Point::where('user_id', $reward_request->user_id)->first();

        if ($reward_request->reward_id) {
            $the_service_provider->update([
                'total_points' => $the_service_provider->total_points + $reward_request->reward->points,
            ]);

        } elseif ($reward_request->service_activity_id) {
            $activities = RequestActivity::where('request_id', $reward_request->id)->get();
            foreach ($activities as $activity) {
                $earned = $activity->serviceActivity->earn_points;
                $the_service_provider->update([
                    'total_points' => $the_service_provider->total_points + $earned,
                ]);
                TrackPoint::create([
                    'point_id' => $point->id,
                    'point' => $earned,
                    'comment' => 'اضافة نقاط بسبب التقديم على خدمة من الأنشطة الترفيهية',
                ]);
            }

        } elseif ($reward_request->service_id) {
            $services = RequestStockPoint::where('request_id', $reward_request->id)->get();
            foreach ($services as $service) {
                $earned = $service->service->point * $service->products_count;
                $the_service_provider->update([
                    'total_points' => $the_service_provider->total_points + $earned,
                ]);
                TrackPoint::create([
                    'point_id' => $point->id,
                    'point' => $earned,
                    'comment' => 'اضافة نقاط بسبب التقديم على خدمة',
                ]);
            }

        } elseif ($reward_request->replace_reward_id) {
            $replaces = RequestReplacePoint::where('request_id', $reward_request->id)->get();
            foreach ($replaces as $replace) {
                $earned = $replace->replaceReward->point * $replace->products_count;
                $the_service_provider->update([
                    'total_points' => $the_service_provider->total_points + $earned,
                ]);
            }

        } elseif ($reward_request->service_tourist_attraction_id) {
            $tourists = RequestTouriste::where('request_id', $reward_request->id)->get();
            foreach ($tourists as $tourist) {
                $earned = $tourist->serviceTouristAttraction->earn_points;
                $the_service_provider->update([
                    'total_points' => $the_service_provider->total_points + $earned,
                ]);
                TrackPoint::create([
                    'point_id' => $point->id,
                    'point' => $earned,
                    'comment' => 'اضافة نقاط بسبب التقديم على المعالم السياحية',
                ]);
            }

        } elseif ($reward_request->requestGuide) {
            $offers = RequestGuide::where('request_id', $reward_request->id)->get();
            foreach ($offers as $offer) {
                $earned = $offer->offer->points;
                $the_service_provider->update([
                    'total_points' => $the_service_provider->total_points + $earned,
                ]);
                TrackPoint::create([
                    'point_id' => $point->id,
                    'point' => $earned,
                    'comment' => 'اضافة نقاط بسبب التقديم على العروض في دليلك',
                ]);
            }

        } else {
            return response()->json([
                'status' => 'Fail',
                'message' => 'نوع الطلب غير معروف',
            ], 422);
        }

        DB::commit();
        return response()->json([
            'status' => 'Success',
            'message' => 'تم إنهاء الطلب بنجاح',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'Fail',
            'message' => 'حدث خطأ: ' . $e->getMessage(),
        ], 500);
    }
}

}
