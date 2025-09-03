<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Email;
use App\Mail\SendEmailMail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\NotificationResource;
use App\Http\Requests\SendNotificationRequest;
use App\Traits\Res;
use Illuminate\Support\Facades\Config;

class NotificationController extends Controller
{
    use Res;

    public function all(Request $request)
    {
        $now = Carbon::now()->toDateTimeString();
        $auth = $request->user();

        $types = [
            'tourist-attractions',
            'entertainment',
            'episodes',
            'participants',
            'guide',
            'replace-points',
            'sponsors',
            'stock-points',
            'tourist-attractions'
        ];
        Notification::where('user_id', $auth->id)->where('read_at', null)->update([
            'read_at' => $now,
        ]);
        $per_page = $request->per_page ?? 12;
        $notifications = Notification::where('user_id', $auth->id)->latest()->paginate($per_page);
        NotificationResource::collection($notifications);

        $data = [
            'types' => $types,
            'notifications' => $notifications,
        ];
        return $this->sendRes('Notifications Returned Successfully', true, $data, [], 200);
    }

    public function countNotification(Request $request)
    {
        $auth = $request->user();
        $notifications = Notification::where('user_id', $auth->id)->where('read_at', null)->count();
        return $this->sendRes('Count Notifications Returned Successfully', true, $notifications, [], 200);
    }

    public function sendNotification(SendNotificationRequest $request)
    {
        $image = $request->file('image') ? $request->file('image')->store('notification') : null;
        $file = $request->file('file') ? $request->file('file')->store('notification') : null;

        if($request->type == 'email')
        {
            $email = Email::findorFail($request->email);
            // تعيين إعدادات SMTP ديناميكياً
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $email->mail_host,
                'username' => $email->name,
                'port' => $email->port,
                'encryption' => $email->encryption,
                'password' => $email->password,
            ]);
            Config::set('mail.from.address', $email->name);

            foreach($request->user_id as $userId)
            {
                $user = User::findorFail($userId);
                $message = $request->message ?? '';
                $subject = $request->subject ?? '';
                $link = $request->link ?? '';
                $image = $image;
                $file = $file;
                Mail::to($user->email)->send(new SendEmailMail($subject,$user, $message, $link, $image, $file, $email));
            }
        }
        else
        {
            foreach($request->user_id as $userId)
            {
                Notification::create([
                    'user_id' => $userId,
                    'message' => $request->message,
                    'link' => $request->link,
                    'image' => $image,
                    'file' => $file,
                ]);
            }
        }
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Send Successfully',
        ]);
    }
}
