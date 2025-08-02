<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function all()
    {
        return response()->json([
            'status' => 'Success',
            'data' => ImageResource::collection(Image::all()),
            'message' => 'Images Returned Successfully',
        ]);
    }

    public function edit(Request $request)
    {
        if (!$request->has('images') || !is_array($request->images)) {
            return response()->json([
                'status' => 'Success',
                'data' => ImageResource::collection(Image::all()),
                'message' => 'No images to edit',
            ]);
        }

        foreach ($request->images as $id => $img) {
            $image = Image::find($id);

            $imagePath = $img instanceof \Illuminate\Http\UploadedFile
                ? $img->store('theImages')
                : ($image ? $image->image : null);

            if ($image) {
                $image->update(['image' => $imagePath]);
            } elseif ($imagePath) {
                Image::create(['image' => $imagePath]);
            }
        }

        return response()->json([
            'status' => 'Success',
            'data' => ImageResource::collection(Image::all()),
            'message' => 'Images Edited Successfully',
        ]);
    }

public function delete($id)
{
    DB::beginTransaction();

    try {
        $image = Image::find($id);
        if ($image) {
            $path = str_replace('storage/', '', $image->image);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $image->delete();

            DB::commit();

            return response()->json([
                'status' => 'Success',
                'data' => ImageResource::collection(Image::all()),
                'message' => 'Image Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Image not found',
            ], 404);
        }
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'Failed',
            'message' => 'An error occurred while deleting the image',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
