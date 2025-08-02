<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditEntertainmentActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'tax' => 'required',
            'address' => 'required',
            'place' => 'required',
            'country' => 'required',
            'website_url' => 'required',
            'appointment' => 'required|date_format:Y-m-d H:i:s',
            'apper_appointment' => 'required|date_format:Y-m-d H:i:s',
            'email' => 'required|email',
            'location' => 'required',
            'send_notification' => 'required|in:0,1',
            'old_files' => 'nullable|array',
            'new_files' => 'nullable|array',
            'old_images' => 'nullable|array',
            'new_images' => 'nullable|array',
            'phone' => 'nullable|array',
            'term' => 'nullable|array',
            'service_type' => 'nullable|array',
            'amount' => 'nullable|array',
            'from' => 'nullable|array',
            'to' => 'nullable|array',
            'earn_points' => 'nullable|array',
            'num_tickets' => 'nullable|array',
            'image_service' => 'nullable|array',
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
