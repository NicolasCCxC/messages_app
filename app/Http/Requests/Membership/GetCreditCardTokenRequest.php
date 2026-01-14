<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;
use phpDocumentor\Reflection\Types\Boolean;

class GetCreditCardTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            "language" => "string|required",
            "command" => "string|required",
            "creditCardToken" => "array|required",
            "creditCardToken.payerId" => "string|required",
            "creditCardToken.name" => "string|required",
            "creditCardToken.identificationNumber" => "string|required",
            "creditCardToken.paymentMethod" => "string|required",
            "creditCardToken.number" => "string|required",
            "creditCardToken.expirationDate" => "string|required",
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            //
        ];
    }
}
