<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditShepherdRequest extends FormRequest
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
            'address' => 'required',
            'side' => 'required',
            'send_notification' => 'required|in:0,1',
            'location' => 'required',
            'website_url' => 'required',
            'phone' => 'nullable|array',
            'old_images' => 'nullable|array',
            'new_images' => 'nullable|array',
            'old_files' => 'nullable|array',
            'new_files' => 'nullable|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
