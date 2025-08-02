<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReplacePointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reward_address' => 'required',
            'reward_description' => 'required',
            'location' => 'required',
            'website_url' => 'required',
            'file' => 'nullable|file',
            'image' => 'nullable|file',
            'send_notification' => 'nullable|in:0,1',
            'have_count' => 'nullable|in:0,1',
            'count_people' => 'required_if:have_count,1',
            'phone' => 'nullable|array',
            'term' => 'nullable|array',
            'name' => 'nullable|array',
            'point' => 'nullable|array',
            'reward_image' => 'nullable|array',
            'qty' => 'nullable|array',
            'available' => 'nullable|array',
            'end_date' => 'nullable|array',
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
