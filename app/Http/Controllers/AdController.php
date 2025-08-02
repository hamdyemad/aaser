<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\AdTerm;
use App\Models\FileAd;
use App\Models\ImageAd;
use App\Models\AdLocation;
use Illuminate\Http\Request;
use App\Http\Requests\AdRequest;
use App\Http\Resources\AdResource;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EditAdRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdController extends Controller
{
    public function add(AdRequest $request)
    {

        $validator = Validator::make($request->all(), [
            'ad_link_type' => ['string', 'max:255', 'in:inside,outside', 'nullable'],
            'ad_link' => ['string', 'max:255', 'nullable'],
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => $validator->errors()->first(),
            ], 422);
        }


        DB::beginTransaction();
        try {
            $ad = Ad::create([
                'title' => $request->title ?? '',
                'timer' => $request->timer ?? '',
                'ad_link_type' => $request->ad_link_type ?? '',
                'ad_link' => $request->ad_link ?? '',
                'description' => $request->description ?? '',
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
            ]);

            if($request->terms)
            {
                foreach($request->terms as $term)
                {
                    AdTerm::create([
                        'ad_id' => $ad->id,
                        'title' => $term,
                    ]);
                }
            }

            foreach($request->location as $location)
            {
                AdLocation::create([
                    'ad_id' => $ad->id,
                    'location' => $location,
                ]);
            }

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('ads');

                    ImageAd::create([
                        'ad_id' => $ad->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('ads');

                    FileAd::create([
                        'ad_id' => $ad->id,
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
            'message' => 'Ad Added Successfully',
        ]);
    }

    public function edit(EditAdRequest $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'ad_link_type' => ['string', 'max:255', 'in:inside,outside', 'nullable'],
            'ad_link' => ['string', 'max:255', 'nullable'],
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $ad = Ad::findorFail($id);

            $ad->update([
                'title' => $request->title ?? '',
                'description' => $request->description ?? '',
                'start_date' => $request->start_date ?? '',
                'end_date' => $request->end_date ?? '',
            ]);

            if($request->terms)
            {
                $ad->terms()->delete();
                foreach($request->terms as $term)
                {
                    AdTerm::create([
                        'ad_id' => $ad->id,
                        'title' => $term,
                    ]);
                }
            }

            if($request->location)
            {
                $ad->locations()->delete();
                foreach($request->location as $location)
                {
                    AdLocation::create([
                        'ad_id' => $ad->id,
                        'location' => $location,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $ad->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $ad->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('ads');
                        $ad->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $ad->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $ad->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('ads');
                        $ad->file()->create([
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
            'message' => 'Ad Edited Successfully',
        ]);
    }

    public function delete($id)
    {
        $ad = Ad::findorFail($id);
        if($ad->image)
        {
            $oldImage = $ad->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($ad->file)
        {
            $oldfile = $ad->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        $ad->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Ad Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $ad = Ad::with('locations','terms','image','file')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new AdResource($ad),
            'message' => 'Ad Returned Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $ads = Ad::with('locations','terms','image','file')->latest()->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => AdResource::collection($ads),
            'message' => 'Ads Returned Successfully',
            'pagination' => [
                'current_page' => $ads->currentPage(),
                'last_page' => $ads->lastPage(),
                'per_page' => $ads->perPage(),
                'total' => $ads->total(),
            ],
        ]);
    }
}
