<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'address' => 'required',
            'side' => 'required',
            'send_notification' => 'required|in:0,1',
            'description' => 'required',
            'location' => 'required',
            'website_url' => 'required',
            'old_files' => 'nullable|array',
            'new_files' => 'nullable|array',
            'old_images' => 'nullable|array',
            'new_images' => 'nullable|array',
            'phone' => 'nullable|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
