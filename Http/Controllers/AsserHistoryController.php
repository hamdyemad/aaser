<?php

namespace App\Http\Controllers;

use App\Models\AsserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AsserHistoryRequest;
use App\Http\Resources\AsserHistoryResource;
use App\Http\Requests\EditAsserHistoryRequest;

class AsserHistoryController extends Controller
{
    public function add(AsserHistoryRequest $request)
    {
        $image = $request->file('image')->store('history');
        AsserHistory::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Asser History Added Successfully',
        ]);
    }

    public function edit(EditAsserHistoryRequest $request, $id)
    {
        $history = AsserHistory::findorFail($id);
        if($request->file('image'))
        {
            Storage::delete($history->image);
        }
        $image = $request->file('image') ? $request->file('image')->store('history') : $history->image;
        $history->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Asser History Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $history = AsserHistory::findorFail($id);
        Storage::delete($history->image);
        $history->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Asser History Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $history = AsserHistory::findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new AsserHistoryResource($history),
            'message' => 'Asser History Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $history = AsserHistory::OrderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => AsserHistoryResource::collection($history),
            'message' => 'All History Return Successfully',
            'pagination' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
            ],
        ]);
    }
}
