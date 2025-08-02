<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'type_id' => 'required|exists:guide_types,id',
            'description' => 'required',
            'address' => 'required',
            'terms' => 'nullable|array',
            'send_notification' => 'required|in:0,1',
            'country' => 'required',
            'location' => 'required',
            'website_url' => 'required',
            'offer_name' => 'nullable|array',
            'offer_discount' => 'nullable|array',
            'offer_num_customers' => 'nullable|array',
            'offer_num_every_customer' => 'nullable|array',
            'offer_points' => 'nullable|array',
            'offer_points' => 'nullable|array',
            'offer_date' => 'nullable|array',
            'old_images' => 'nullable|array',
            'new_images' => 'nullable|array',
            'old_files' => 'nullable|array',
            'new_files' => 'nullable|array',
            'phone' => 'nullable|array',
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
