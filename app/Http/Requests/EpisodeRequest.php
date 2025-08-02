<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EpisodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'title' => 'required',
            'send_notification' => 'required|in:0,1',
            'earn_points' => 'required',
            'description' => 'required',
            'file' => 'nullable|file',
            'image' => 'nullable|file',
            'appointment' => 'required||date_format:Y-m-d H:i:s',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
