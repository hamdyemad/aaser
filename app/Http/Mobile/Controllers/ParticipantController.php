<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
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
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Traits\Res;
use Carbon\Carbon;

class ParticipantController extends Controller
{
    use Res;

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 12;

        $participants = Participant::with('phone','image','file')->OrderBy('id','desc')->paginate($per_page);
        ParticipantResource::collection($participants);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'particiants');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => $ads,
            'participants' => $participants,
        ];

        return $this->sendRes('All Participants Return Successfully', true, $data, [], 200);
    }


    public function show($id)
    {
        $participant = Participant::with('phone','image','file')->find($id);
        if($participant) {
            return $this->sendRes('Participant Return Successfully', true, new ParticipantResource($participant), [], 200);
        } else {
            return $this->sendRes('Participant not found', false, [], [], 404);
        }
    }


}
