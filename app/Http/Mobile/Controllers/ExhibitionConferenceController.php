<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Mobile\Requests\AddParticipantRequest;
use App\Http\Mobile\Requests\AddVisitorRequest;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\ProviderConference;
use Illuminate\Support\Facades\DB;
use App\Models\ExhibitionConference;
use App\Models\ProviderConferencePhone;
use Illuminate\Support\Facades\Storage;
use App\Models\FileExhibitionConference;
use App\Models\EmailExhibitionConference;
use App\Models\ImageExhibitionConference;
use App\Models\PhoneExhibitionConference;
use App\Models\VisitorExhibitionConference;
use App\Http\Resources\RewardRequestResource;
use App\Models\ParticipantExhibitionConference;
use App\Http\Requests\ExhibitionConferenceRequest;
use App\Http\Resources\ExhibitionConferenceResource;
use App\Http\Requests\EditExhibitionConferenceRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\ServiceProvider;
use App\Traits\Res;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ExhibitionConferenceController extends Controller
{
    use Res;


    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $page = $request->page ?? 1;

        $ExhibitionConference = ExhibitionConference::with('phone','email','image','file','visitor','participant','provider')
        ->latest()->paginate($per_page);
        ExhibitionConferenceResource::collection($ExhibitionConference);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'exhibition_conference');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => [],
            'exhibition_conference' => $ExhibitionConference,
        ];

        if($page <= 1) {
            $data['ads'] = $ads;
        }

        return $this->sendRes('All Exhibition Conference Return Successfully', true, $data, [], 200);

    }

    public function show($id)
    {
        $ExhibitionConference = ExhibitionConference::with('phone','email','image','file','visitor','participant','provider')->find($id);
        if($ExhibitionConference) {
            return $this->sendRes('Exhibition Conference Return Successfully', true, new ExhibitionConferenceResource($ExhibitionConference), [], 200);
        } else {
            return $this->sendRes('Exhibition Conference not found', false, [], [], 404);
        }
    }

    public function addVisitor(AddVisitorRequest $request)
    {

        $visitor = VisitorExhibitionConference::create([
            'conference_id' => $request->exhibition_conference_id,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'jop' => $request->jop,
        ]);

        $maxRequestId = RewardRequest::max('request_id');
        $request_id = $maxRequestId ? $maxRequestId + 1 : rand(1000, 9999);

        $reward_request = RewardRequest::create([
            'visitor_exhibition_conference_id' => $visitor->id,
            'user_id' => auth()->id(),
            'request_id' => $request_id,
        ]);

        return $this->sendRes('Visitor And Reward Request Added Successfully', true, [], [], 200);
    }

    public function addParticipant(AddParticipantRequest $request)
    {

        $participant = ParticipantExhibitionConference::create([
            'conference_id' => $request->exhibition_conference_id,
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'activity' => $request->activity,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'website_url' => $request->website_url,
            'registeration_type' => $request->registeration_type,
        ]);

        $maxRequestId = RewardRequest::max('request_id');
        $request_id = $maxRequestId ? $maxRequestId + 1 : rand(1000, 9999);
        $reward_request = RewardRequest::create([
            'participant_exhibition_conference_id' => $participant->id,
            'user_id' => auth()->id(),
            'request_id' => $request_id,
        ]);

        return $this->sendRes('Participant And Reward Request Added Successfully', true, [], [], 200);
    }


    public function add(ExhibitionConferenceRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $randomEmail = 'provider' . Str::uuid() . '@asser-service-provider.com';
            $randomPasswordPlain = Str::random(16);
            $randomPasswordHashed = Hash::make($randomPasswordPlain);

            $exhibition_conference = ExhibitionConference::create([
                'name' => $request->name,
                'country' => $request->country,
                'website_url' => $request->website_url,
                'description' => $request->description,
                'address' => $request->address,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'send_notification' => $request->send_notification,
                'earn_points' => $request->earn_points,
                'appointment' => $request->appointment,
                'apper_appointment' => $request->apper_appointment,
            ]);

            $service_provider = ServiceProvider::create([
                'name' => 'مقدم خدمة ' . $exhibition_conference->name,
                'email' => $randomEmail,
                'password' => $randomPasswordHashed,
                'unencrypted_password' => $randomPasswordPlain,
                'phone' => $request->phone[0] ?? null,
                'side' => $exhibition_conference->country,
                'active' => 1,
                'total_points' => 0,
                'specialized_provider' => 1,
                'specialized_type' => 'exhibition-conference',
                'specialized_id' => $exhibition_conference->id,
            ]);

            if($request->provider_name)
            {
                $provider = ProviderConference::create([
                    'conference_id' => $exhibition_conference->id,
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
                        ProviderConferencePhone::create([
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
                        'message' => 'يوجد معرض أو مؤتمر جديد '. $exhibition_conference->name,
                        'page' => 'entertainment',
                        'product_name' => $exhibition_conference->name,
                        'product_id' => $exhibition_conference->id,
                    ]);
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    PhoneExhibitionConference::create([
                        'exhibition_conference_id' => $exhibition_conference->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->email)
            {
                foreach($request->email as $email)
                {
                    EmailExhibitionConference::create([
                        'exhibition_conference_id' => $exhibition_conference->id,
                        'email' => $email,
                    ]);
                }
            }

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $uploadedImage) {
                    $storedPath = $uploadedImage->store('ExhibitionConference');

                    ImageExhibitionConference::create([
                        'exhibition_conference_id' => $exhibition_conference->id,
                        'image' => $storedPath,
                    ]);
                }
            }

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $uploadedfile) {
                    $storedPath = $uploadedfile->store('ExhibitionConference');

                    FileExhibitionConference::create([
                        'exhibition_conference_id' => $exhibition_conference->id,
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
            'message' => 'Exhibition Conference Added Successfully',
        ]);
    }

    public function edit(EditExhibitionConferenceRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $ExhibitionConference = ExhibitionConference::findorFail($id);
            $ExhibitionConference->update([
                'name' => $request->name,
                'country' => $request->country,
                'website_url' => $request->website_url,
                'description' => $request->description,
                'address' => $request->address,
                'send_notification' => $request->send_notification,
                'location' => $request->location,
                'location_link' => $request->location_link,
                'earn_points' => $request->earn_points,
                'appointment' => $request->appointment,
                'apper_appointment' => $request->apper_appointment,
            ]);

            if($request->provider_name)
            {
                $ExhibitionConference->provider()->delete();
                $provider = ProviderConference::create([
                    'conference_id' => $ExhibitionConference->id,
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
                        ProviderConferencePhone::create([
                            'provider_id' => $provider->id,
                            'phone' => $phone,
                        ]);
                    }
                }
            }

            if($request->phone)
            {
                foreach($request->phone as $phone)
                {
                    $ExhibitionConference->phone()->delete();
                    PhoneExhibitionConference::create([
                        'exhibition_conference_id' => $ExhibitionConference->id,
                        'phone' => $phone,
                    ]);
                }
            }

            if($request->email)
            {
                $ExhibitionConference->email()->delete();
                foreach($request->email as $email)
                {
                    EmailExhibitionConference::create([
                        'exhibition_conference_id' => $ExhibitionConference->id,
                        'email' => $email,
                    ]);
                }
            }

            if($request->file('new_images'))
            {
                $oldImageIds = $request->input('old_images', []);
                $newImages = $request->file('new_images');
                $imagesToDelete = $ExhibitionConference->image()->whereNotIn('id', $oldImageIds)->get();
                foreach ($imagesToDelete as $gallery) {
                    Storage::delete($gallery->image);
                }
                $ExhibitionConference->image()->whereNotIn('id', $oldImageIds)->delete();
                if ($newImages) {
                    foreach ($newImages as $image) {
                        $imagePath = $image->store('ExhibitionConference');
                        $ExhibitionConference->image()->create([
                            'image' => $imagePath,
                        ]);
                    }
                }
            }

            if($request->file('new_files'))
            {
                $oldFileIds = $request->input('old_files', []);
                $newFiles = $request->file('new_files');
                $filesToDelete = $ExhibitionConference->file()->whereNotIn('id', $oldFileIds)->get();
                foreach ($filesToDelete as $gallery) {
                    Storage::delete($gallery->file);
                }
                $ExhibitionConference->file()->whereNotIn('id', $oldFileIds)->delete();
                if ($newFiles) {
                    foreach ($newFiles as $file) {
                        $filePath = $file->store('ExhibitionConference');
                        $ExhibitionConference->file()->create([
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
            'message' => 'Exhibition Conference Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $ExhibitionConference = ExhibitionConference::findorFail($id);
        if($ExhibitionConference->image)
        {
            $oldImage = $ExhibitionConference->image()->get();
            foreach ($oldImage as $gallery) {
                Storage::delete($gallery->image);
            }
        }
        if($ExhibitionConference->file)
        {
            $oldfile = $ExhibitionConference->file()->get();
            foreach ($oldfile as $gallery) {
                Storage::delete($gallery->file);
            }
        }
        $ExhibitionConference->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Exhibition Conference Deleted Successfully',
        ]);
    }




    public function allSite(Request $request)
    {
        $item = $request->item ?? 20;
        $now = Carbon::now()->toDateString();
        $ExhibitionConference = ExhibitionConference::whereDate('apper_appointment','<=',$now)->OrderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => ExhibitionConferenceResource::collection($ExhibitionConference),
            'message' => 'All Exhibition Conference Return Successfully',
            'pagination' => [
                'current_page' => $ExhibitionConference->currentPage(),
                'last_page' => $ExhibitionConference->lastPage(),
                'per_page' => $ExhibitionConference->perPage(),
                'total' => $ExhibitionConference->total(),
            ],
        ]);
    }




}
