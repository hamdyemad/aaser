<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TouristAttractionController extends Controller
{
public function add(TouristAttractionRequest $request)
{
    DB::beginTransaction();
    try
    {
        $randomEmail = 'provider' . Str::uuid() . '@asser-service-provider.com';
        $randomPasswordPlain = Str::random(16);
        $randomPasswordHashed = Hash::make($randomPasswordPlain);

        $tourist_attraction = TouristAttraction::create([
            'name' => $request->name,
            'description' => $request->description,
            'tax' => $request->tax,
            'location' => $request->location,
            'location_link' => $request->location_link,
            'website_url' => $request->website_url,
            'country' => $request->country,
            'address' => $request->address,
            'send_notification' => $request->send_notification,
            'hours_work' => $request->hours_work,
            'service_vendor_email' => $randomEmail,
            'service_vendor_password' => $randomPasswordPlain,
        ]);

        $service_provider = ServiceProvider::create([
            'name' => 'مقدم خدمة ' . $tourist_attraction->name,
            'email' => $randomEmail,
            'password' => $randomPasswordHashed,
            'unencrypted_password' => $randomPasswordPlain,
            'phone' => $request->phone[0] ?? null,
            'side' => $tourist_attraction->country,
            'active' => 1,
            'total_points' => 0,
            'specialized_provider' => 1,
            'specialized_type' => 'tourist-attraction',
            'specialized_id' => $tourist_attraction->id,
        ]);

        if($request->send_notification == 1)
        {
            $users = User::all();
            foreach($users as $user)
            {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => 'يوجد معلم سياحي جديد '. $tourist_attraction->name,
                    'page' => 'tourist-attractions',
                    'product_name' => $tourist_attraction->name,
                    'product_id' => $tourist_attraction->id,
                ]);
            }
        }

        if($request->phone)
        {
            foreach($request->phone as $phone)
            {
                PhoneTouristAttraction::create([
                    'tourist_attraction_id' => $tourist_attraction->id,
                    'phone' => $phone,
                ]);
            }
        }

        if($request->term)
        {
            foreach($request->term as $term)
            {
                TermTouristAttraction::create([
                    'tourist_attraction_id' => $tourist_attraction->id,
                    'term' => $term,
                ]);
            }
        }

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $uploadedImage) {
                $storedPath = $uploadedImage->store('attraction');

                ImageTouristAttraction::create([
                    'tourist_attraction_id' => $tourist_attraction->id,
                    'image' => $storedPath,
                ]);
            }
        }

        if($request->provider_name)
        {
            $provider = ProviderTouriste::create([
                'touriste_id' => $tourist_attraction->id,
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
                    ProviderTouristePhone::create([
                        'provider_id' => $provider->id,
                        'phone' => $phone,
                    ]);
                }
            }
        }

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $uploadedfile) {
                $storedPath = $uploadedfile->store('attraction');

                FileTouristAttraction::create([
                    'tourist_attraction_id' => $tourist_attraction->id,
                    'file' => $storedPath,
                ]);
            }
        }

        if($request->service_name)
        {
            $service_images = $request->service_image ?? [];
            foreach($request->service_name as $key => $serviceName)
            {
                $service_image = isset($service_images[$key]) && $service_images[$key]
                    ? $service_images[$key]->store('attraction')
                    : null;

                $count = $request->service_count[$key] ?? 0;
                $before_tax = $request->service_price[$key] ?? 0;
                $tax_value = ($before_tax * ($tourist_attraction->tax / 100));
                $final_price = $before_tax + $tax_value;

                ServiceTouristAttraction::create([
                    'tourist_attraction_id' => $tourist_attraction->id,
                    'name' => $serviceName,
                    'image' => $service_image,
                    'before_tax' => $before_tax,
                    'price' => $final_price,
                    'appointment' => $request->appointment[$key] ?? '',
                    'date' => $request->service_date[$key] ?? '',
                    'earn_points' => $request->service_earn_points[$key] ?? '',
                    'count' => $count,
                    'available_count' => $count,
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
        'message' => 'Tourist Attraction Added Successfully',
        'generated_email' => $randomEmail,
        'generated_password' => $randomPasswordPlain, // ← للعرض المؤقت فقط إن أردت
    ]);
}

    public function edit(EditTouristAttractionRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $tourist_attraction = TouristAttraction::findorFail($id);
            $tourist_attraction->update([
                'name' => $request->name,
                'description' => $request->description,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'tax' => $request->tax,
                'website_url' => $request->website_url,
                'country' => $request->country,
                'address' => $request->address,
                'send_notification' => $request->send_notification,
                'hours_work' => $request->hours_work,
            ]);

            if($request->phone)
            {
                $tourist_attraction->phone()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneTouristAttraction::create([
                        'tourist_attraction_id' => $tourist_attraction->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->provider_name)
            {
                $tourist_attraction->provider()->delete();
                $provider = ProviderTouriste::create([
                    'touriste_id' => $tourist_attraction->id,
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
                        ProviderTouristePhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }

            if($request->term)
            {
                $tourist_attraction->term()->delete();
                foreach($request->term as $term)
                {
                    TermTouristAttraction::create([
                        'tourist_attraction_id' => $tourist_attraction->id,
                        'term' => $term,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $tourist_attraction->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $tourist_attraction->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('attraction');
                        $tourist_attraction->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $tourist_attraction->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $tourist_attraction->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('attraction');
                        $tourist_attraction->file()->create([
                            'file' => $filePath,
                        ]);
                    }
                }
            }

            if($request->service_name)
            {
                $tourist_attraction->service()->delete();
                $service_images = $request->service_image ?? [];
                foreach($request->service_name as $key => $serviceName)
                {
                    $service_image = isset($service_images[$key]) && $service_images[$key]
                        ? $service_images[$key]->store('attraction')
                        : null;
                    ServiceTouristAttraction::create([
                        'tourist_attraction_id' => $tourist_attraction->id,
                        'name' => $serviceName,
                        'image' => $service_image,
                        'before_tax' => $request->service_price[$key],
                        'price' => ($request->service_price[$key] * ($tourist_attraction->tax / 100)) + $request->service_price[$key],
                        'appointment' => $request->appointment[$key],
                        'date' => $request->service_date[$key],
                        'earn_points' => $request->service_earn_points[$key],
                        'count' => $request->service_count[$key],
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
            'message' => 'Tourist Attraction Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $tourist_attraction = TouristAttraction::findorFail($id);
        if($tourist_attraction->image)
        {
            $oldImage = $tourist_attraction->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($tourist_attraction->file)
        {
            $oldfile = $tourist_attraction->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        if($tourist_attraction->service)
        {
            $oldService = $tourist_attraction->service()->get();
            foreach ($oldService as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        $tourist_attraction->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Tourist Attraction Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $tourist_attraction = TouristAttraction::with('phone','service','term','image','file','provider')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new TouristAttractionResource($tourist_attraction),
            'message' => 'Tourist Attraction Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $tourist_attractions = TouristAttraction::with('phone','service','term','image','file','provider')->OrderBy('id','desc')
        ->when($request->filled('search'),function($query) use ($request){
            return $query->where('name', 'LIKE', "%{$request->search}%");
        })
        ->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => TouristAttractionResource::collection($tourist_attractions),
            'message' => 'All Tourist Attractions Return Successfully',
            'pagination' => [
                'current_page' => $tourist_attractions->currentPage(),
                'last_page' => $tourist_attractions->lastPage(),
                'per_page' => $tourist_attractions->perPage(),
                'total' => $tourist_attractions->total(),
            ],
        ]);
    }

    // public function touristeService(Request $request)
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
    //             RequestTouriste::create([
    //                 'request_id' => $reward_request->id,
    //                 'service_tourist_attraction_id' => $serviceId,
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

public function touristeService(Request $request)
{
    DB::beginTransaction();
    try {

        // التحقق من أن المستخدم مسجل دخول
        if (!auth('user')->check()) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'You Should Be User',
            ], 422);
        }

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
                if(!$service_tourist_attraction) {
                    $validator->errors()->add("service.$index", "The service not found");
                }
                $qty = $quantities[$index] ?? 0;
                $available = $service_tourist_attraction->available_count;

                if ($available !== null && $qty > $available) {
                    $validator->errors()->add("qty.$index", "The quantity for service ID $serviceId exceeds available stock ($available).");
                }
                if(!empty($dates)) {
                    $service_date_incoming = Carbon::parse($dates[$index]);
                    $service_end_date =  Carbon::parse($service_tourist_attraction->date);
                    if($service_end_date->lt($service_date_incoming)) {
                        $validator->errors()->add("date", "the date of service has reached the end date");
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

        $auth = $request->user();
        $request_id = rand(1000, 9999);

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

        DB::commit();

        return response()->json([
            'status' => 'Success',
            'data' => new RewardRequestResource($reward_request),
            'message' => 'Service Request Added Successfully',
        ]);

    } catch (\Exception $exception) {
        DB::rollBack();
        return response()->json([
            'status' => 'Error',
            'message' => $exception->getMessage(),
        ], 500);
    }
}



}
