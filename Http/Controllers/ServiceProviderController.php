<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RewardRequest;
use App\Models\ServiceProvider;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ServiceProviderRequest;
use App\Http\Resources\ServiceProviderResource;
use App\Http\Requests\EditServiceProviderRequest;
use App\Http\Requests\LoginServiceProviderRequest;

class ServiceProviderController extends Controller
{
    public function add(ServiceProviderRequest $request)
    {
        ServiceProvider::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unencrypted_password' => Hash::make($request->unencrypted_password),
            'side' => $request->side,
            'phone' => $request->phone,
            'active' => $request->active,
            'specialized_provider' => $request->specialized_provider,
            'specialized_type' => $request->specialized_type,
            'specialized_id' => $request->specialized_id,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Service Provider Added Successfully',
        ]);
    }

    public function edit(EditServiceProviderRequest $request, $id)
    {
        $service_provider = ServiceProvider::findorFail($id);
        $password = $request->password ? Hash::make($request->password) : $service_provider->password;
        $service_provider->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'unencrypted_password' => Hash::make($request->unencrypted_password),
            'side' => $request->side,
            'phone' => $request->phone,
            'active' => $request->active,
            'specialized_provider' => $request->specialized_provider,
            'specialized_type' => $request->specialized_type,
            'specialized_id' => $request->specialized_id,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Service Provider Edit Successfully',
        ]);
    }

    public function delete($id)
    {
        $service_provider = ServiceProvider::findorFail($id);
        $service_provider->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Service Provider Deleted Successfully',
        ]);
    }

    public function show($id)
    {
        $service_provider = ServiceProvider::findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new ServiceProviderResource($service_provider),
            'message' => 'ServiceProvider Return Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $specialized_type = request()->specialized_type ?? '';
        $service_providers = ServiceProvider::OrderBy('id','desc')
        ->when($specialized_type, function ($query) use ($specialized_type) {
            return $query->where('specialized_type', $specialized_type);
        })->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => ServiceProviderResource::collection($service_providers),
            'message' => 'All Service Providers Return Successfully',
            'pagination' => [
                'current_page' => $service_providers->currentPage(),
                'last_page' => $service_providers->lastPage(),
                'per_page' => $service_providers->perPage(),
                'total' => $service_providers->total(),
            ],
        ]);
    }

    public function Login(LoginServiceProviderRequest $request)
    {
        $service_provider = ServiceProvider::where('email', $request->email)->first();
        if (!$service_provider || !Hash::check($request->password, $service_provider->password)) {
            return response()->json([
                'status' => "Fail",
                'data' => null,
                'message' => 'Invalid Email Or Password'
            ], 422);
        }
        if($service_provider->active == 0)
        {
            return response()->json([
                'status' => "Fail",
                'data' => null,
                'message' => 'Your Email Not Active'
            ], 422);
        }
        $token = $service_provider->createToken($service_provider->name);
        return response()->json([
            'token' => $token->plainTextToken,
            'status' => "Success",
            'data' => new ServiceProviderResource($service_provider),
            'message' => 'Login Successfully'
        ]);
    }

    public function serviceProviderPeople(Request $request)
    {
        $item = $request->item ?? 20;
        $auth = $request->user();
        $reward_requests = RewardRequest::where('done_by_service_provider', $auth->id)->pluck('user_id')->toArray();
        $users = User::whereIn('id', $reward_requests)->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => UserResource::collection($users),
            'message' => 'All Users For This Service Providers Return Successfully',
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function lastActive(Request $request)
    {
        $auth = $request->user();
        if(!$request->last_active)
        {
            return response()->json([
                'status' => "Fail",
                'data' => [],
                'message' => 'The Last Active Must Be Data And Time Like This 2025-04-25 15:10:00'
            ], 422);
        }
        ServiceProvider::findorFail($auth->id)->update([
            'last_active' => $request->last_active,
        ]);

        return response()->json([
            'status' => "Success",
            'data' => [],
            'message' => 'The Last Active Updated Successfully'
        ]);
    }
}
