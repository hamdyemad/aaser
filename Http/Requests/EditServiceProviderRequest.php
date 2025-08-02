<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class EditServiceProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->user()->id;
        return [
            'name' => 'required',
            'email' => 'required|email|unique:service_providers,email,' . $id,
            'password' => 'nullable',
            'phone' => 'required|numeric|unique:service_providers,phone,' . $id,
            'side' => 'required',
            'active' => 'required|in:0,1',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
