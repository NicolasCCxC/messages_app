<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class PayWithTokenRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'TX_VALUE' => 'required|array',
            'TX_VALUE.value' => 'required|numeric',
            'TX_VALUE.currency' => 'required|string',
            'TX_TAX' => 'required|array',
            'TX_TAX.value' => 'required|numeric',
            'TX_TAX.currency' => 'required|string',
            'TX_TAX_RETURN_BASE' => 'required|array',
            'TX_TAX_RETURN_BASE.value' => 'required|numeric',
            'TX_TAX_RETURN_BASE.currency' => 'required|string',
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
