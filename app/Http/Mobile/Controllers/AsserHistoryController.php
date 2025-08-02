<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AsserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AsserHistoryRequest;
use App\Http\Resources\AsserHistoryResource;
use App\Http\Requests\EditAsserHistoryRequest;
use App\Traits\Res;

class AsserHistoryController extends Controller
{
    use Res;

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;

        $histories = AsserHistory::latest()->paginate($per_page);
        AsserHistoryResource::collection($histories);
        return $this->sendRes('All History Return Successfully', true, $histories, [], 200);
    }

    public function show($id)
    {
        $history = AsserHistory::find($id);
        if($history) {
            return $this->sendRes('Asser History Return Successfully', true, new AsserHistoryResource($history), [], 200);
        } else {
            return $this->sendRes('Asser History not found', false, [], [], 404);
        }
    }


}
