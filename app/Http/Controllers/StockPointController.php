<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class StockPointController extends Controller
{
    public function add(StockPointRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $randomEmail = 'provider' . Str::uuid() . '@asser-service-provider.com';
            $randomPasswordPlain = Str::random(16);
            $randomPasswordHashed = Hash::make($randomPasswordPlain);

            $stock_point = StockPoint::create([
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'tax' => $request->tax,
                'website_url' => $request->website_url,
                'send_notification' => $request->send_notification,
                'have_count' => $request->have_count,
                'count_people' => $request->count_people,
            ]);

            $service_provider = ServiceProvider::create([
                'name' => 'مقدم خدمة ' . $stock_point->company_name,
                'email' => $randomEmail,
                'password' => $randomPasswordHashed,
                'unencrypted_password' => $randomPasswordPlain,
                'phone' => $request->phone[0] ?? null,
                'side' => $stock_point->location,
                'active' => 1,
                'total_points' => 0,
                'specialized_provider' => 1,
                'specialized_type' => 'stoke-points',
                'specialized_id' => $stock_point->id,
            ]);

            if($request->provider_name)
            {
                $provider = ProviderStock::create([
                    'stock_id' => $stock_point->id,
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
                        ProviderStockPhone::create([
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
                        'message' => 'يوجد منتج جديد '. $stock_point->company_name,
                        'page' => 'view-products',
                        'product_name' => $stock_point->company_name,
                        'product_id' => $stock_point->id,
                    ]);
                }
            }

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('stock');

                    ImageStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('stock');

                    FileStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'file' => $storedPath,
                    ]);
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->term)
            {
                foreach($request->term as $term)
                {
                    TermStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'term' => $term,
                    ]);
                }
            }

            if($request->name)
            {
                $service_images = $request->service_image ?? [];
                foreach($request->name as $key => $name)
                {
                    $service_image = isset($service_images[$key]) && $service_images[$key]
                        ? $service_images[$key]->store('stock')
                        : null;

                    $count = $request->count[$key] ?? 0;
                    ServiceStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'name' => $name,
                        'amount' => $request->amount[$key],
                        'point' => $request->point[$key],
                        'count' => $count,
                        'available_count' => $count,
                        'before_price' => $request->before_price[$key],
                        'after_price' => $request->after_price[$key],
                        'date' => $request->date[$key],
                        'appointment' => $request->appointment[$key],
                        'image' => $service_image,
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
            'message' => 'Stock Point Added Successfully',
        ]);
    }

    public function edit(StockPointRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $stock_point = StockPoint::findorFail($id);
            $stock_point->update([
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'tax' => $request->tax,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
            ]);

            if($request->provider_name)
            {
                $stock_point->provider()->delete();
                $provider = ProviderStock::create([
                    'stock_id' => $stock_point->id,
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
                        ProviderStockPhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }

            if($request->phone)
            {
                $stock_point->phones()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->term)
            {
                $stock_point->terms()->delete();
                foreach($request->term as $term)
                {
                    TermStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'term' => $term,
                    ]);
                }
            }

            if($request->name)
            {
                $stock_point->services()->delete();
                $service_images = $request->service_image ?? [];
                foreach($request->name as $key => $name)
                {
                    $service_image = isset($service_images[$key]) && $service_images[$key]
                        ? $service_images[$key]->store('stock')
                        : null;
                    $count = $request->count[$key] ?? 0;

                    ServiceStockPoint::create([
                        'stock_point_id' => $stock_point->id,
                        'name' => $name,
                        'amount' => $request->amount[$key],
                        'point' => $request->point[$key],
                        'count' => $count,
                        'available_count' => $count,
                        'before_price' => $request->before_price[$key],
                        'after_price' => $request->after_price[$key],
                        'date' => $request->date[$key],
                        'appointment' => $request->appointment[$key],
                        'image' => $service_image,
                    ]);
                }
            }

            if($request->file('image'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('image');
                $imagesToDelete = $stock_point->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $stock_point->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('stock');
                        $stock_point->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('file'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('file');
                $filesToDelete = $stock_point->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $stock_point->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('stock');
                        $stock_point->file()->create([
                            'file' => $filePath,
                        ]);
                    }
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $stock_point->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $stock_point->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('stock');
                        $stock_point->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $stock_point->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $stock_point->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('stock');
                        $stock_point->file()->create([
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
            'message' => 'Stock Point Edited Successfully',
        ]);
    }

    public function delete($id)
    {
        $stock_point = StockPoint::findorFail($id);
        if($stock_point->image)
        {
            $oldImage = $stock_point->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($stock_point->file)
        {
            $oldfile = $stock_point->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        $stock_point->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Stock Point Deleted Successfully',
        ]);
    }


    public function show($id)
    {
        $stock_point = StockPoint::with('phones','terms','services','image','file','provider')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new StockPointResource($stock_point),
            'message' => 'Stock Point Retuned Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $stock_point = StockPoint::with('phones','terms','services','image','file','provider')->orderBy('id', 'desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => StockPointResource::collection($stock_point),
            'message' => 'Stock Points Retuned Successfully',
            'pagination' => [
                'current_page' => $stock_point->currentPage(),
                'last_page' => $stock_point->lastPage(),
                'per_page' => $stock_point->perPage(),
                'total' => $stock_point->total(),
            ],
        ]);
    }

    public function addServiceRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => ['array', 'required'],
            'service.*' => ['required', 'exists:service_stock_points,id'],
            'products_count' => ['array', 'required'],
            'products_count.*' => ['integer', 'min:1'],
            'date' => ['array', 'required'],
            'date.*' => ['required', 'date', 'date_format:Y-m-d']
        ]);

        $validator->after(function ($validator) use ($request) {
            $serviceIds = $request->input('service', []);
            $products_count = $request->input('products_count', []);
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

                if($serviceStockPoint->available_count < $products_count[$index]) {
                    $validator->errors()->add("products_count", "the available count of service stock point has reach the maximum");
                }

                if($serviceStockPoint->available_count < 1) {
                    $validator->errors()->add("date", "the available count of service stock point has reach the maximum");
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
            $auth = $request->user();
            $request_id = rand(1000,9999);
            $reward_request = RewardRequest::create([
                'user_id' => $auth->id,
                'request_id' => $request_id,
            ]);
            foreach($request->service as $key => $serviceId)
            {
                $count = $request->products_count[$key];
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
            return response()->json([
                'status' => 'Success',
                'data' => new RewardRequestResource($reward_request),
                'message' => 'Service Request Added Successfully',
            ]);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
