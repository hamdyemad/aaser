<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Episode;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\EpisodeRequest;
use App\Http\Resources\EpisodeResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditEpisodeRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{

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
        return response()->json([
            'status' => 'Success',
            'data' => new EpisodeResource($episode),
            'message' => 'Episode Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $episodes = Episode::OrderBy('id','desc')
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => EpisodeResource::collection($episodes),
            'message' => 'All Episodes Return Successfully',
            'pagination' => [
                'current_page' => $episodes->currentPage(),
                'last_page' => $episodes->lastPage(),
                'per_page' => $episodes->perPage(),
                'total' => $episodes->total(),
            ],
        ]);
    }
}
