<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockPointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required',
            'company_address' => 'required',
            'location' => 'required',
            'tax' => 'required',
            'website_url' => 'required',
            'send_notification' => 'nullable|in:0,1',
            'have_count' => 'nullable|in:0,1',
            'count_people' => 'required_if:have_count,1',
            'phone' => 'nullable|array',
            'term' => 'nullable|array',
            'name' => 'nullable|array',
            'amount' => 'nullable|array',
            'point' => 'nullable|array',
            'service_image' => 'nullable|array',
            'before_price' => 'nullable|array',
            'after_price' => 'nullable|array',
            'date' => 'nullable|array',
            'file' => 'nullable|array',
            'image' => 'nullable|array',
            'old_files' => 'nullable|array',
            'old_images' => 'nullable|array',
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
