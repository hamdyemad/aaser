<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shepherd;
use App\Models\FileShepherd;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ImageShepherd;
use App\Models\PhoneShepherd;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ShepherdRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ShepherdResource;
use App\Http\Requests\EditShepherdRequest;

class ShepherdController extends Controller
{
    public function add(ShepherdRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $shepherd = Shepherd::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'side' => $request->side,
                'send_notification' => $request->send_notification,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
            ]);

            if($request->send_notification == 1)
            {
                $users = User::all();
                foreach($users as $user)
                {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => 'يوجد راعي جديد '. $shepherd->name,
                        'page' => 'sponsors',
                        'product_name' => $shepherd->name,
                        'product_id' => $shepherd->id,
                    ]);
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneShepherd::create([
                        'shepherd_id' => $shepherd->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if ($request->hasFile('image'))
            {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('shepherd');

                    ImageShepherd::create([
                        'shepherd_id' => $shepherd->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file'))
            {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('shepherd');

                    FileShepherd::create([
                        'shepherd_id' => $shepherd->id,
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
            'message' => 'Shepherd Added Successfully',
        ]);
    }

    public function edit(EditShepherdRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $shepherd = Shepherd::findorFail($id);
            $shepherd->update([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'side' => $request->side,
                'send_notification' => $request->send_notification,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
            ]);

            if($request->phone)
            {
                $shepherd->phone()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneShepherd::create([
                        'shepherd_id' => $shepherd->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $shepherd->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $shepherd->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('shepherd');
                        $shepherd->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $shepherd->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $shepherd->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('shepherd');
                        $shepherd->file()->create([
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
            'message' => 'Shepherd Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $shepherd = Shepherd::findorFail($id);
        if($shepherd->image)
        {
            $oldImage = $shepherd->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($shepherd->file)
        {
            $oldfile = $shepherd->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        $shepherd->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Shepherd Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $shepherd = Shepherd::with('phone','image','file')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new ShepherdResource($shepherd),
            'message' => 'Shepherd Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $shepherds = Shepherd::with('phone','image','file')->OrderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => ShepherdResource::collection($shepherds),
            'message' => 'All Shepherds Return Successfully',
            'pagination' => [
                'current_page' => $shepherds->currentPage(),
                'last_page' => $shepherds->lastPage(),
                'per_page' => $shepherds->perPage(),
                'total' => $shepherds->total(),
            ],
        ]);
    }
}
