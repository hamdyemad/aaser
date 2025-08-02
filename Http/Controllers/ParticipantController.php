<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Participant;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\FileParticipant;
use App\Models\ImageParticipant;
use App\Models\PhoneParticipant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ParticipantRequest;
use App\Http\Resources\ParticipantResource;
use App\Http\Requests\EditParticipantRequest;

class ParticipantController extends Controller
{
    public function add(ParticipantRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $participant = Participant::create([
                'name' => $request->name,
                'address' => $request->address,
                'side' => $request->side,
                'description' => $request->description,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
                'send_notification' => $request->send_notification,
            ]);

            if($request->send_notification == 1)
            {
                $users = User::all();
                foreach($users as $user)
                {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => 'يوجد مشارك جديد '. $participant->name,
                        'page' => 'participants',
                        'product_name' => $participant->name,
                        'product_id' => $participant->id,
                    ]);
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneParticipant::create([
                        'participant_id' => $participant->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if ($request->hasFile('image'))
            {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('participant');

                    ImageParticipant::create([
                        'participant_id' => $participant->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file'))
            {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('participant');

                    FileParticipant::create([
                        'participant_id' => $participant->id,
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
            'message' => 'Participant Added Successfully',
        ]);
    }

    public function edit(EditParticipantRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $participant = Participant::findorFail($id);
            $participant->update([
                'name' => $request->name,
                'address' => $request->address,
                'side' => $request->side,
                'description' => $request->description,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'website_url' => $request->website_url,
                'send_notification' => $request->send_notification,
            ]);

            if($request->phone)
            {
                $participant->phone()->delete();
                foreach($request->phone as $phone)
                {
                    PhoneParticipant::create([
                        'participant_id' => $participant->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $participant->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $participant->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('participant');
                        $participant->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $participant->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $participant->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('participant');
                        $participant->file()->create([
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
            'message' => 'Participant Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $participant = Participant::findorFail($id);
        if($participant->image)
        {
            $oldImage = $participant->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($participant->file)
        {
            $oldfile = $participant->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        $participant->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Participant Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $participant = Participant::with('phone','image','file')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new ParticipantResource($participant),
            'message' => 'Participant Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $participants = Participant::with('phone','image','file')->OrderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => ParticipantResource::collection($participants),
            'message' => 'All Participants Return Successfully',
            'pagination' => [
                'current_page' => $participants->currentPage(),
                'last_page' => $participants->lastPage(),
                'per_page' => $participants->perPage(),
                'total' => $participants->total(),
            ],
        ]);
    }
}
