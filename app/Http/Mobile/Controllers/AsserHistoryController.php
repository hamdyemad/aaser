<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AsserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AsserHistoryRequest;
use App\Http\Resources\AsserHistoryResource;
use App\Http\Requests\EditAsserHistoryRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Traits\Res;
use Carbon\Carbon;

class AsserHistoryController extends Controller
{
    use Res;

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 10;

        $histories = AsserHistory::latest()->paginate($per_page);
        AsserHistoryResource::collection($histories);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'asser_history');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => $ads,
            'histories' => $histories,
        ];
        return $this->sendRes('All History Return Successfully', true, $data, [], 200);
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
