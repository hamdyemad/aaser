<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'terms' => 'nullable|array',
            'location' => 'required',
            'image' => 'required|file',
            'points' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'send_notification' => 'nullable|in:0,1',
            'have_count' => 'nullable|in:0,1',
            'count_people' => 'required_if:have_count,1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
