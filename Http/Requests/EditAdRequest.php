<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'terms' => 'nullable|array',
            'description' => 'required',
            'new_images' => 'nullable|array',
            'new_files' => 'nullable|array',
            'old_images' => 'nullable|array',
            'old_files' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'location' => 'nullable|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
