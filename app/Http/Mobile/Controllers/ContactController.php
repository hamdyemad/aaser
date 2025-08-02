<?php

namespace App\Http\Mobile\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Mobile\Requests\AddContactRequest;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ContactAnswer;
use App\Http\Resources\ContactResource;
use App\Http\Requests\ContactAnswerRequest;
use App\Traits\Res;

class ContactController extends Controller
{
    use Res;
    public function addContact(AddContactRequest $request)
    {
        $auth = $request->user();
        $contact = Contact::create([
            'user_id' => $auth->id,
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'localtion_url' => $request->localtion_url,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'phone' => $request->phone,
        ]);


        return $this->sendRes('Contact Added Successfully', true, new ContactResource($contact), [], 200);

    }

    public function addAnswer(ContactAnswerRequest $request)
    {
        $answer = ContactAnswer::create([
            'contact_id' => $request->contact_id,
            'answer' => $request->answer,
        ]);
        $contact = Contact::findorFail($request->contact_id);
        Notification::create([
            'user_id' => $contact->user_id,
            'message' => $answer->answer,
        ]);
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Contact Answer Added Successfully',
        ]);
    }

    public function allContacts(Request $request)
    {
        $item = $request->item ?? 20;
        $contact = Contact::with('answer','user')->orderBy('id','desc')->paginate($item);
        return response()->json([
            'status' => 'Success',
            'data' => ContactResource::collection($contact),
            'message' => 'Contacts Returned Successfully',
            'pagination' => [
                'current_page' => $contact->currentPage(),
                'last_page' => $contact->lastPage(),
                'per_page' => $contact->perPage(),
                'total' => $contact->total(),
            ],
        ]);
    }

    public function viewContact($id)
    {
        $contact = Contact::with('answer','user')->findorFail($id);
        return response()->json([
            'status' => 'Success',
            'data' => new ContactResource($contact),
            'message' => 'Contact Returned Successfully',
        ]);
    }

    public function deleteContact($id)
    {
        $contact = Contact::findorFail($id);
        $contact->delete();
        return response()->json([
            'status' => 'Success',
            'data' => [],
            'message' => 'Contact Deleted Successfully',
        ]);
    }
}
