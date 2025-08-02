<?php

namespace App\Http\Mobile\Requests;

use App\Traits\Res;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddParticipantRequest extends FormRequest
{
    use Res;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exhibition_conference_id' => 'required|exists:exhibition_conferences,id',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'activity' => 'required',
            'company_name' => 'required',
            'address' => 'required',
            'website_url' => 'required',
            'registeration_type' => 'required|in:sponsor,participant',
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
