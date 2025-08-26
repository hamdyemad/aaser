<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shepherd;
use App\Models\FileShepherd;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ImageShepherd;
use App\Models\PhoneShepherd;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ShepherdRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ShepherdResource;
use App\Http\Requests\EditShepherdRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Traits\Res;
use Carbon\Carbon;

class ShepherdController extends Controller
{
    use Res;

    public function all(Request $request)
    {
        $per_page = $request->per_page ?? 20;
        $shepherds = Shepherd::with('image','image','file')->latest()->paginate($per_page);
        ShepherdResource::collection($shepherds);

        $ads = Ad::with('terms','image','file')
        ->whereDate('end_date', '>', Carbon::now())
        ->whereDate('start_date', '<', Carbon::now())
        ->whereHas('locations', function($q) {
            $q->where('location', 'sponsor');
        })->latest()->get();
        $ads = AdResource::collection($ads);

        $data = [
            'ads' => $ads,
            'shepherds' => $shepherds,
        ];


        return $this->sendRes('All Shepherds Return Successfully', true, $data, [], 200);
    }



    public function show($id)
    {
        $shepherd = Shepherd::with('phone','image','file')->find($id);
        if($shepherd) {
            return $this->sendRes('Shepherd Return Successfully', true,new ShepherdResource($shepherd) , [], 200);
        } else {
            return $this->sendRes('Shepherd not found', false, [], [], 404);
        }


    }


}
