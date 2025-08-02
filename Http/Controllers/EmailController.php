<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use App\Http\Requests\EmailRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailResource;

class EmailController extends Controller
{
    public function add(EmailRequest $request)
    {
        Email::create([
            'name' => $request->name,
            'mail_host' => $request->mail_host,
            'password' => $request->password,
            'port' => $request->port,
            'encryption' => $request->encryption,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Email Added Successfully',
        ]);
    }
    
    public function addencwioath(EmailRequest $request)
    {
        Email::create([
            'name' => $request->name,
            'mail_host' => $request->mail_host,
            'password' => $request->password,
            'port' => $request->port,
            'encryption' => $request->encryption,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Email Added Successfully',
        ]);
    }

    public function edit(EmailRequest $request, $id)
    {
        $email = Email::findorFail($id);
        $email->update([
            'name' => $request->name,
            'mail_host' => $request->mail_host,
            'password' => $request->password,
            'port' => $request->port,
            'encryption' => $request->encryption,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Email Edited Successfully',
        ]);
    }

    public function delete($id)
    {
        $email = Email::findorFail($id);
        $email->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Email Deleted Successfully',
        ]);
    }

    public function all(Request $request)
    {
        $item = $request->item ?? 20;
        $emails = Email::orderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => EmailResource::collection($emails),
            'message' => 'Emails Returned Successfully',
            'pagination' => [
                'current_page' => $emails->currentPage(),
                'last_page' => $emails->lastPage(),
                'per_page' => $emails->perPage(),
                'total' => $emails->total(),
            ],
        ]);
    }
}
