<?php

namespace App\Http\Mobile\Requests;

use App\Traits\Res;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConfirmCodeRequest extends FormRequest
{
    use Res;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|exists:users,email',
            'code' => 'required|numeric',
            'password' => ['required', 'confirmed'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all(); // returns all error messages as an array
        $combinedMessage = implode('\n', $errors); // join all messages in one line
        throw new HttpResponseException(
            $this->sendRes($combinedMessage, false, [], $validator->errors(), 422)
        );
    }
}
