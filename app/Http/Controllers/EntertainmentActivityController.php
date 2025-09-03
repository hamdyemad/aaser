<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EntertainmentActivityController extends Controller
{
    public function add(EntertainmentActivityRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $randomEmail = 'provider' . Str::uuid() . '@asser-service-provider.com';
            $randomPasswordPlain = Str::random(16);
            $randomPasswordHashed = Hash::make($randomPasswordPlain);

            $entertainment_activity = EntertainmentActivity::create([
                'name' => $request->name,
                'email' => $request->email,
                'tax' => $request->tax,
                'appointment' => $request->appointment,
                'apper_appointment' => $request->apper_appointment,
                'address' => $request->address,
                'place' => $request->place,
                'send_notification' => $request->send_notification,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'description' => $request->description,
                'country' => $request->country,
                'website_url' => $request->website_url,
            ]);

            $service_provider = ServiceProvider::create([
                'name' => 'مقدم خدمة ' . $entertainment_activity->name,
                'email' => $randomEmail,
                'password' => $randomPasswordHashed,
                'unencrypted_password' => $randomPasswordPlain,
                'phone' => $request->phone[0] ?? null,
                'side' => $entertainment_activity->country,
                'active' => 1,
                'total_points' => 0,
                'specialized_provider' => 1,
                'specialized_type' => 'entertainment-activity',
                'specialized_id' => $entertainment_activity->id,
            ]);

            if($request->provider_name)
            {
                $provider = ProviderActivitie::create([
                    'activitie_id' => $entertainment_activity->id,
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
                        ProviderActivitiePhone::create([
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
                        'message' => 'يوجد فعالية ترفيهية جديدة '. $entertainment_activity->name,
                        'page' => 'entertainment',
                        'product_name' => $entertainment_activity->name,
                        'product_id' => $entertainment_activity->id,
                    ]);
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneEntertainmentActivity::create([
                        'activitie_id' => $entertainment_activity->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->term)
            {
                foreach($request->term as $term)
                {
                    TermEntertainmentActivity::create([
                        'activitie_id' => $entertainment_activity->id,
                        'term' => $term,
                    ]);
                }
            }

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('EntertainmentActivity');

                    ImageEntertainmentActivity::create([
                        'activitie_id' => $entertainment_activity->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('EntertainmentActivity');

                    FileEntertainmentActivity::create([
                        'activitie_id' => $entertainment_activity->id,
                        'file' => $storedPath,
                    ]);
                }
            }

            if($request->service_type)
            {
                $image_services = $request->image_service ?? [];
                foreach($request->service_type as $key => $serviceType)
                {
                    $image_service = isset($image_services[$key]) && $image_services[$key]
                        ? $image_services[$key]->store('EntertainmentActivity')
                        : null;
                    ServiceEntertainmentActivity::create([
                        'activitie_id' => $entertainment_activity->id,
                        'service_type' => $serviceType,
                        'amount' => $request->amount[$key],
                        'from' => $request->from[$key],
                        'to' => $request->to[$key],
                        'earn_points' => $request->earn_points[$key],
                        'num_tickets' => $request->num_tickets[$key],
                        'available_num_tickets' => $request->num_tickets[$key],
                        'image' => $image_service,
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
            'message' => 'Entertainment Activity Added Successfully',
        ]);
    }

    public function edit(EditEntertainmentActivityRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $EntertainmentActivity = EntertainmentActivity::findorFail($id);
            $EntertainmentActivity->update([
                'name' => $request->name,
                'tax' => $request->tax,
                'email' => $request->email,
                'appointment' => $request->appointment,
                'apper_appointment' => $request->apper_appointment,
                'address' => $request->address,
                'place' => $request->place,
                'send_notification' => $request->send_notification,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'description' => $request->description,
                'country' => $request->country,
                'website_url' => $request->website_url,
            ]);

            if($request->provider_name)
            {
                $EntertainmentActivity->provider()->delete();
                $provider = ProviderActivitie::create([
                    'activitie_id' => $EntertainmentActivity->id,
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
                        ProviderActivitiePhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }

            if($request->phone)
            {
                $EntertainmentActivity->phone()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneEntertainmentActivity::create([
                        'activitie_id' => $EntertainmentActivity->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->term)
            {
                $EntertainmentActivity->term()->delete();
                foreach($request->term as $term)
                {
                    TermEntertainmentActivity::create([
                        'activitie_id' => $EntertainmentActivity->id,
                        'term' => $term,
                    ]);
                }
            }

            if($request->service_type)
            {
                $EntertainmentActivity->service()->delete();
                $image_services = $request->image_service ?? [];
                foreach($request->service_type as $key => $serviceType)
                {
                    $image_service = isset($image_services[$key]) && $image_services[$key]
                        ? $image_services[$key]->store('EntertainmentActivity')
                        : null;
                    ServiceEntertainmentActivity::create([
                        'activitie_id' => $EntertainmentActivity->id,
                        'service_type' => $serviceType,
                        'amount' => $request->amount[$key],
                        'from' => $request->from[$key],
                        'to' => $request->to[$key],
                        'earn_points' => $request->earn_points[$key],
                        'num_tickets' => $request->num_tickets[$key],
                        'available_num_tickets' => $request->num_tickets[$key],
                        'image' => $image_service,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $EntertainmentActivity->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $EntertainmentActivity->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('EntertainmentActivity');
                        $EntertainmentActivity->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $EntertainmentActivity->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $EntertainmentActivity->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('EntertainmentActivity');
                        $EntertainmentActivity->file()->create([
                            'file' => $filePath,
                        ]);
                    }
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
            'message' => 'Entertainment Activity Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $EntertainmentActivity = EntertainmentActivity::findorFail($id);
        if($EntertainmentActivity->image)
        {
            $oldImage = $EntertainmentActivity->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($EntertainmentActivity->file)
        {
            $oldfile = $EntertainmentActivity->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        if($EntertainmentActivity->service)
        {
            $oldService = $EntertainmentActivity->service()->get();
            foreach ($oldService as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        $EntertainmentActivity->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Entertainment Activity Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $EntertainmentActivity = EntertainmentActivity::with('phone','term','file','image','service','provider')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new EntertainmentActivityResource($EntertainmentActivity),
            'message' => 'Entertainment Activity Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $EntertainmentActivity = EntertainmentActivity::with('phone','term','file','image','service','provider')->OrderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => EntertainmentActivityResource::collection($EntertainmentActivity),
            'message' => 'All Entertainment Activity Return Successfully',
            'pagination' => [
                'current_page' => $EntertainmentActivity->currentPage(),
                'last_page' => $EntertainmentActivity->lastPage(),
                'per_page' => $EntertainmentActivity->perPage(),
                'total' => $EntertainmentActivity->total(),
            ],
        ]);
    }

    // public function activityService(Request $request)
    // {
    //     DB::beginTransaction();
    //     try
    //     {
    //         if(!auth('user')->check())
    //         {
    //             return response()->json([
    //                 'status' => 'Fail',
    //                 'data' => [],
    //                 'message' => 'You Should Be User',
    //             ], 422);
    //         }
    //         $auth = $request->user();
    //         $request_id = rand(1000,9999);
    //         $reward_request = RewardRequest::create([
    //             'user_id' => $auth->id,
    //             'request_id' => $request_id,
    //         ]);
    //         foreach($request->service as $serviceId)
    //         {
    //             RequestActivity::create([
    //                 'request_id' => $reward_request->id,
    //                 'service_activity_id' => $serviceId,
    //             ]);
    //         }
    //         DB::commit();
    //         return response()->json([
    //             'status' => 'Success',
    //             'data' => new RewardRequestResource($reward_request),
    //             'message' => 'Service Request Added Successfully',
    //         ]);
    //     }
    //     catch (\Exception $exception) {
    //         DB::rollBack();
    //         return $exception->getMessage();
    //     }
    // }


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

        if(count($serviceIds) > 0) {
            foreach ($serviceIds as $index => $serviceId) {
                $service = ServiceEntertainmentActivity::find($serviceId);
                if($service) {
                    $service_from =  Carbon::parse($service->from);
                    if($service->available_num_tickets < $qty[$index]) {
                        $validator->errors()->add("qty.$index", "the qty is bigger than available tickets");
                    }

                    if($service->available_num_tickets < 1) {
                        $validator->errors()->add("service.$index", "the service of entertainment is not available");
                    }

                    $service_date_incoming = Carbon::parse($dates[$index]);
                    $service_to =  Carbon::parse($service->to);
                    if(!($service_from->lte($service_date_incoming) && $service_to->gte($service_date_incoming))) {
                        $validator->errors()->add("date", "the service of entertainment date invalid please check it again");
                    }
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
        if (!auth('user')->check()) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'You Should Be User',
            ], 422);
        }

        $auth = $request->user();
        $maxRequestId = RewardRequest::max('request_id');
        $request_id = $maxRequestId ? $maxRequestId + 1 : rand(1000, 9999);
        $reward_request = RewardRequest::create([
            'user_id' => $auth->id,
            'request_id' => $request_id,
        ]);

        $services = $request->input('service'); // service[]
        $qtys = $request->input('qty');
        $dates = $request->input('date');

        if (!is_array($services) || !is_array($qtys) || count($services) !== count($qtys)) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'Invalid service or qty format',
            ], 422);
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

        DB::commit();

        return response()->json([
            'status' => 'Success',
            'data' => new RewardRequestResource($reward_request),
            'message' => 'Service Request Added Successfully',
        ]);
    } catch (\Exception $exception) {
        DB::rollBack();
        return response()->json([
            'status' => 'Fail',
            'message' => $exception->getMessage(),
        ], 500);
    }
}


}
