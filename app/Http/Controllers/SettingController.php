<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Resources\SettingResource;
use App\Http\Requests\EditSettingRequest;

class SettingController extends Controller
{
    public function show(Request $request)
    {
        $setting = Setting::all();
        return response()->json([
            'status' => 'Success',
            'data' => SettingResource::collection($setting),
            'message' => 'Setting Returned Successfully'
        ]);
    }

    public function edit(EditSettingRequest $request)
    {
        $data = $request->validated();
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        return response()->json([
            'status' => 'Success',
            'data' => SettingResource::collection(Setting::all()),
            'message' => 'Setting Edited Successfully'
        ]);
    }
}
