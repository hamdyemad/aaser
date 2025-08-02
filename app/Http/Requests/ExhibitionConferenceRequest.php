<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExhibitionConferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'country' => 'required',
            'website_url' => 'required',
            'appointment' => 'required|date_format:Y-m-d H:i:s',
            'apper_appointment' => 'required|date_format:Y-m-d H:i:s',
            'description' => 'required',
            'earn_points' => 'required',
            'file' => 'nullable|array',
            'image' => 'nullable|array',
            'address' => 'required',
            'phone' => 'nullable|array',
            'location' => 'required',
            'email' => 'nullable|array',
            // provider
            'provider_name' => 'nullable',
            'provider_address' => 'nullable',
            'provider_website' => 'nullable',
            'provider_location' => 'nullable',
            'provider_num_hours' => 'nullable',
            'provider_phone' => 'nullable|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
