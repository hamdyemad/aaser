<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Mobile\Requests\AddUserRequest;
use App\Http\Mobile\Requests\ConfirmCodeRequest;
use App\Http\Mobile\Requests\LoginUserRequest;
use App\Models\User;
use App\Models\Guide;
use App\Models\Point;
use App\Models\Episode;
use App\Models\Setting;
use App\Models\GuideRate;
use App\Models\TrackPoint;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ResetPassword;
use App\Mail\ResetPasswordMail;
use App\Models\TouristAttraction;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\PointResource;
use App\Http\Requests\EditUserRequest;
use App\Models\TouristeAttractionRate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\AddPointToUserRequest;
use App\Http\Resources\RequestReplacePointResource;
use App\Http\Resources\RewardRequestResource;
use App\Models\RequestReplacePoint;
use App\Models\RewardRequest;
use App\Traits\Res;
use Exception;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use Res;


    // Start Authentication
    public function register(AddUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address ?? '',
            'password' => Hash::make($request->password),
        ]);
        $point = Point::create([
            'user_id' => $user->id,
        ]);
        $register_point = Setting::where('key','register_point')->first();
        $track_point = TrackPoint::create([
            'point_id' => $point->id,
            'point' => $register_point->value,
            'comment' => $register_point->value . ' نقاط تسجيل دخول',
        ]);

        $token = $user->createToken($user->name);
        $data = [
            'token' => $token->plainTextToken,
            'data' => new UserResource(User::findorFail($user->id)),
        ];

        return $this->sendRes('User Added Successfully', true, $data, [], 200);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => "Fail",
                'data' => null,
                'message' => 'Invalid Email Or Password'
            ], 422);
        }
        $token = $user->createToken($user->name);
        $data = [
            'token' => $token->plainTextToken,
            'user' => new UserResource(User::findorFail($user->id)),
        ];

        return $this->sendRes('Login Successfully', true, $data, [], 200);

    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email',$request->email)->first();
        $code = rand(1000,9999);
        ResetPassword::create([
            'email' => $request->email,
            'code' => $code
        ]);

        $data = [
            'email' => $request->email,
            'code' => $code,
        ];
        Mail::to($user->email)->send(new ResetPasswordMail($data));
        try {
        } catch(Exception $e) {

        }
        return $this->sendRes('Please Check Your Email', true, [], [], 200);

    }

    public function confirmCode(ConfirmCodeRequest $request)
    {
        $reset_password = ResetPassword::where(['email' => $request->email,'code' => $request->code])->first();
        if($reset_password)
        {
            $user = User::where('email',$request->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            $reset_password->delete();
            return $this->sendRes('Password Reset Successfully', true, [], [], 200);
        }
        else
        {
            return $this->sendRes('Code Not Match', false, [], [], 400);
        }
    }

    public function getProfile(Request $request)
    {
        $auth = auth()->user();
        return $this->sendRes('Profile Returned Successfully', true, new UserResource($auth), [], 200);
    }

    public function editProfile(Request $request)
    {

        $auth = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|unique:users,email,' . $auth->id,
            'phone' => 'nullable|unique:users,phone,' . $auth->id,
            'password' => ['nullable','confirmed'],
            'image' => 'nullable|image',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all(); // returns all error messages as an array
            $combinedMessage = implode('\n', $errors); // join all messages in one line
            return $this->sendRes($combinedMessage, false, [], $validator->errors(), 422);
        }

        $data = [];
        ($request->name) ? $data['name'] = $request->name : null;
        ($request->email) ? $data['email'] = $request->email : null;
        ($request->phone) ? $data['phone'] = $request->phone : null;
        ($request->address) ? $data['address'] = $request->address : null;

        if ($request->file('image')) {
            if ($auth->image) {
                Storage::delete($auth->image);
            }
            $data['image'] = $request->file('image')->store('users');
        }
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $auth->update($data);
        return $this->sendRes('Profile Updated Successfully', true, new UserResource($auth), [], 200);

    }

    // End Authentication



    public function addPointToUser(AddPointToUserRequest $request)
    {
        $point = Point::where('user_id',$request->user_id)->first();
        $track_point = TrackPoint::create([
            'point_id' => $point->id,
            'point' => $request->point,
            'comment' => $request->comment,
            'episode_id' => $request->episode_id,
        ]);
        Notification::create([
            'user_id' => $request->user_id,
            'message' => $request->comment,
            'episode_id' => $request->episode_id,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Point Added To User Successfully',
        ]);
    }

    public function addPointForUser(Request $request)
    {
        if(!$request->type)
        {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'You Should Enter The Type For Add The Point',
            ], 422);
        }
        $auth = $request->user();
        if(auth('user')->check())
        {
            $point = Point::where('user_id',$auth->id)->first();
            $view_point = Setting::where('key', $request->type)->first();
            $track_point = TrackPoint::create([
                'point_id' => $point->id,
                'point' => $view_point->value,
                'comment' => $view_point->value . ' نقاط بسبب ' . $request->type,
            ]);
            return response()->json([
                'status' => "Success",
                'data' => [],
                'message' => 'Points Added Successfully'
            ]);
        }
        return response()->json([
            'status' => 'Fail',
            'data' => [],
            'message' => 'You Should Be User',
        ], 422);
    }

    public function viewPoints(Request $request)
    {
        $auth = $request->user();
        $per_page = $request->per_page ?? 10;
        $points = Point::with(['tracking' => function ($query) {
            $query->latest();
        }])->where('user_id',$auth->id)->latest()->get();
        PointResource::collection($points);
        return $this->sendRes('Point For The User Returned Successfully', true, $points, [], 200);
    }


    public function trackReplacePoints(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $auth = $request->user();
        $replace_points = RequestReplacePoint::with('rewardRequest')->whereHas('rewardRequest', function ($query) use ($auth) {
            $query->where('user_id', $auth->id);
        })->latest()->paginate($per_page);

        RequestReplacePointResource::collection($replace_points);

        return $this->sendRes('Replace Points Tracked Successfully', true, $replace_points, [], 200);
    }

    public function rewardRequests(Request $request) {

        $per_page = $request->per_page ?? 10;
        $reward_requests = RewardRequest::with(['provider'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($per_page);
        RewardRequestResource::collection($reward_requests);
        return $this->sendRes('Reward Requests Retrieved Successfully', true, $reward_requests, [], 200);
    }





}
