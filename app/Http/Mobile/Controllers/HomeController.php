<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntertainmentActivityResource;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\ExhibitionConferenceResource;
use App\Http\Resources\GuideResource;
use App\Http\Resources\ParticipantResource;
use App\Http\Resources\ShepherdResource;
use App\Http\Resources\TouristAttractionResource;
use App\Models\EntertainmentActivity;
use App\Models\Episode;
use App\Models\ExhibitionConference;
use App\Models\Guide;
use App\Models\Participant;
use App\Models\Shepherd;
use App\Models\TouristAttraction;
use App\Traits\Res;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{
    use Res;

    public function index(Request $request) {

        $per_page = $request->per_page ?? 12;

        $episdoe_page = $request->episdoe_page ?? 1;
        $tourist_attraction_page = $request->tourist_attraction_page ?? 1;
        $shepherd_page = $request->shepherd_page ?? 1;
        $participant_page = $request->participant_page ?? 1;
        $guide_page = $request->guide_page ?? 1;
        $exhibition_conference_page = $request->exhibition_conference_page ?? 1;
        $entertainment_activity_page = $request->entertainment_activity_page ?? 1;



        $episodes = Episode::latest()->paginate($per_page, ['*'], $episdoe_page);
        EpisodeResource::collection($episodes);

        $tourist_attractions = TouristAttraction::with('phone','service','image','file')
        ->latest()
        ->paginate($per_page, ['*'], $tourist_attraction_page);
        TouristAttractionResource::collection($tourist_attractions);

        $shepherds = Shepherd::with('image')->latest()->paginate($per_page, ['*'], $shepherd_page);
        ShepherdResource::collection($shepherds);

        $participants = Participant::with('phone','image','file')->OrderBy('id','desc')->paginate($per_page, ['*'], $participant_page);
        ParticipantResource::collection($participants);

        $guides = Guide::with('type','phone','image','file')->latest()->paginate($per_page, ['*'], $guide_page);
        GuideResource::collection($guides);


        $exhibition_conferences = ExhibitionConference::with('phone','email','image','file')
        ->latest()->paginate($per_page, ['*'], $exhibition_conference_page);
        ExhibitionConferenceResource::collection($exhibition_conferences);


        $entertainment_activities = EntertainmentActivity::with('phone','file','image')
        ->latest()->paginate($per_page, ['*'], $entertainment_activity_page);
        EntertainmentActivityResource::collection($entertainment_activities);

        $data = [
            'episodes' => $episodes,
            'tourist_attractions' => $tourist_attractions,
            'shepherds' => $shepherds,
            'participants' => $participants,
            'guides' => $guides,
            'exhibition_conferences' => $exhibition_conferences,
            'entertainment_activities' => $entertainment_activities,
        ];
        return $this->sendRes('Data Retrived Success', true, $data, [], 200);


        // return response()->json([
        //     'status' => 'Success',
        //     'data' => EpisodeResource::collection($episodes),
        //     'message' => 'All Episodes Return Successfully',
        //     'pagination' => [
        //         'current_page' => $episodes->currentPage(),
        //         'last_page' => $episodes->lastPage(),
        //         'per_page' => $episodes->perPage(),
        //         'total' => $episodes->total(),
        //     ],
        // ]);



    }
}
