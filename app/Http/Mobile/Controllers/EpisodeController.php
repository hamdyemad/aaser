<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Episode;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\EpisodeRequest;
use App\Http\Resources\EpisodeResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditEpisodeRequest;
use App\Traits\Res;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{

    use Res;

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 20;
        $episodes = Episode::latest()->paginate($per_page);
        EpisodeResource::collection($episodes);

        return $this->sendRes('All Episodes Return Successfully', true, $episodes, [], 200);
    }

    public function add(EpisodeRequest $request)
    {
        $file = $request->file ? $request->file('file')->store('episodes') : null;
        $image = $request->image ? $request->file('image')->store('episodes') : null;
        $episode = Episode::create([
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'file' => $file,
            'image' => $image,
            'appointment' => $request->appointment,
            'send_notification' => $request->send_notification,
            'earn_points' => $request->earn_points,
        ]);
        if($request->send_notification == 1)
        {
            $users = User::all();
            foreach($users as $user)
            {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => 'يوجد حلقة جديده '. $episode->name,
                    'page' => 'episodes',
                    'product_name' => $episode->name,
                    'product_id' => $episode->id,
                ]);
            }
        }

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Episode Added Successfully',
        ]);
    }

    public function edit(EditEpisodeRequest $request, $id)
    {
        $episode = Episode::findorFail($id);
        if($request->file('file'))
        {
            if($episode->file)
            {
                Storage::delete($episode->file);
            }
        }
        $file = $request->file('file') ? $request->file('file')->store('episodes') : $episode->file;
        if($request->file('image'))
        {
            if($episode->image)
            {
                Storage::delete($episode->image);
            }
        }
        $image = $request->file('image') ? $request->file('image')->store('episodes') : $episode->image;
        $episode->update([
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'file' => $file,
            'image' => $image,
            'appointment' => $request->appointment,
            'send_notification' => $request->send_notification,
            'earn_points' => $request->earn_points,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Episode Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $episode = Episode::findorFail($id);
        if($episode->file)
        {
            Storage::delete($episode->file);
        }
        if($episode->image)
        {
            Storage::delete($episode->image);
        }
        $episode->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Episode Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $episode = Episode::findorFail($id);
        $episode = new EpisodeResource($episode);

        return $this->sendRes('Episode Return Successfully', true, $episode, [], 200);


    }


}
