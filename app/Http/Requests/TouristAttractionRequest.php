<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TouristAttractionRequest extends FormRequest
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
            'location' => 'required',
            'website_url' => 'required',
            'tax' => 'required',
            'country' => 'required',
            'address' => 'required',
            'send_notification' => 'required|in:0,1',
            'hours_work' => 'required',
            'file' => 'nullable|array',
            'image' => 'nullable|array',
            'term' => 'nullable|array',
            'phone' => 'nullable|array',
            'service_name' => 'nullable|array',
            'service_image' => 'nullable|array',
            'service_price' => 'nullable|array',
            'service_date' => 'nullable|array',
            'service_earn_points' => 'nullable|array',
            'service_count' => 'nullable|array',
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
