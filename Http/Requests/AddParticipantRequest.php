<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exhibition_conference_id' => 'required|exists:exhibition_conferences,id',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'activity' => 'required',
            'company_name' => 'required',
            'address' => 'required',
            'website_url' => 'required',
            'registeration_type' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
