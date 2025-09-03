<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\GuideTypeResource;
use App\Models\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GuideController extends Controller
{
    public function add(GuideRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $randomEmail = 'provider' . Str::uuid() . '@asser-service-provider.com';
            $randomPasswordPlain = Str::random(16);
            $randomPasswordHashed = Hash::make($randomPasswordPlain);

            $guide = Guide::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'send_notification' => $request->send_notification,
                'country' => $request->country,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
                'type_id' => $request->type_id,
            ]);

            $service_provider = ServiceProvider::create([
                'name' => 'مقدم خدمة ' . $guide->name,
                'email' => $randomEmail,
                'password' => $randomPasswordHashed,
                'unencrypted_password' => $randomPasswordPlain,
                'phone' => $request->phone[0] ?? null,
                'side' => $guide->country,
                'active' => 1,
                'total_points' => 0,
                'specialized_provider' => 1,
                'specialized_type' => 'guide',
                'specialized_id' => $guide->id,
            ]);


            if($request->provider_name)
            {
                $provider = ProviderGuide::create([
                    'guide_id' => $guide->id,
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
                        ProviderGuidePhone::create([
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
                        'message' => 'يوجد دليل جديد '. $guide->name,
                        'page' => 'guide',
                        'product_name' => $guide->name,
                        'product_id' => $guide->id,
                    ]);
                }
            }

            if($request->offer_name)
            {
                $offer_images = $request->offer_image ?? [];
                foreach($request->offer_name as $key => $offerName)
                {
                    $offer_image = isset($offer_images[$key]) && $offer_images[$key]
                    ? $offer_images[$key]->store('guides')
                    : null;
                    GuideOffer::create([
                        'guide_id' => $guide->id,
                        'name' => $offerName,
                        'discount' => $request->offer_discount[$key],
                        'num_customers' => $request->offer_num_customers[$key],
                        'num_every_customer' => $request->offer_num_every_customer[$key],
                        'points' => $request->offer_points[$key],
                        'date' => $request->offer_date[$key],
                        'image' => $offer_image,
                    ]);
                }
            }

            if($request->terms)
            {
                foreach($request->terms as $term)
                {
                    GuideTerm::create([
                        'guide_id' => $guide->id,
                        'title' => $term,
                    ]);
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneGuide::create([
                        'guide_id' => $guide->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if ($request->hasFile('image'))
            {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('guides');

                    ImageGuide::create([
                        'guide_id' => $guide->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file'))
            {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('guides');

                    FileGuide::create([
                        'guide_id' => $guide->id,
                        'file' => $storedPath,
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
            'message' => 'Guide Added Successfully',
        ]);
    }

    public function edit(EditGuideRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $Guide = Guide::findorFail($id);
            $Guide->update([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'send_notification' => $request->send_notification,
                'country' => $request->country,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
                'type_id' => $request->type_id,
            ]);

            if($request->provider_name)
            {
                $Guide->provider()->delete();
                $provider = ProviderGuide::create([
                    'guide_id' => $Guide->id,
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
                        ProviderGuidePhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }

            if($request->offer_name)
            {
                $Guide->offers()->delete();
                $offer_images = $request->offer_image ?? [];
                foreach($request->offer_name as $key => $offerName)
                {
                    $offer_image = isset($offer_images[$key]) && $offer_images[$key]
                    ? $offer_images[$key]->store('guides')
                    : null;
                    GuideOffer::create([
                        'guide_id' => $Guide->id,
                        'name' => $offerName,
                        'discount' => $request->offer_discount[$key],
                        'num_customers' => $request->offer_num_customers[$key],
                        'num_every_customer' => $request->offer_num_every_customer[$key],
                        'points' => $request->offer_points[$key],
                        'date' => $request->offer_date[$key],
                        'image' => $offer_image,
                    ]);
                }
            }

            if($request->terms)
            {
                $Guide->terms()->delete();
                foreach($request->terms as $term)
                {
                    GuideTerm::create([
                        'guide_id' => $Guide->id,
                        'title' => $term,
                    ]);
                }
            }

            if($request->phone)
            {
                $Guide->phone()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneGuide::create([
                        'guide_id' => $Guide->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $Guide->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $Guide->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('guides');
                        $Guide->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $Guide->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $Guide->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('guides');
                        $Guide->file()->create([
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
            'message' => 'Guide Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $Guide = Guide::findorFail($id);
        if($Guide->image)
        {
            $oldImage = $Guide->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($Guide->file)
        {
            $oldfile = $Guide->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        if($Guide->offers)
        {
            $oldOffer = $Guide->offers()->get();
            foreach ($oldOffer as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        $Guide->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Guide Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $Guide = Guide::with('type', 'offers','terms','phone','image','file','provider')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new GuideResource($Guide),
            'message' => 'Guide Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $Guide = Guide::with('type', 'offers','terms','phone','image','file','provider')
        ->when($request->filled('type_id'), function($query) use($request){
            return $query->where('type_id', $request->type_id);
        })
        ->OrderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => GuideResource::collection($Guide),
            'message' => 'All Guide Return Successfully',
            'pagination' => [
                'current_page' => $Guide->currentPage(),
                'last_page' => $Guide->lastPage(),
                'per_page' => $Guide->perPage(),
                'total' => $Guide->total(),
            ],
        ]);
    }

    public function allTypes(Request $request)
    {
        $item = $request->item ?? 20;
        $all = GuideType::paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => GuideTypeResource::collection($all),
            'message' => 'All Guide Return Successfully',
            'pagination' => [
                'current_page' => $all->currentPage(),
                'last_page' => $all->lastPage(),
                'per_page' => $all->perPage(),
                'total' => $all->total(),
            ],
        ]);
    }

    public function addType(Request $request)
    {
        GuideType::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Type Added Successfully',
        ]);
    }

    public function deleteType($id)
    {
        $type = GuideType::findorFail($id);
        $type->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Type Deleted Successfully',
        ]);
    }
}
