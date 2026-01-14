<?php

namespace App\Http\Requests\Gateway;

use Illuminate\Foundation\Http\FormRequest;

class CashTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'shopping_cart' => 'required|array',
            'shopping_cart.id' => 'required|uuid',
            'shopping_cart.total_value' => 'required|numeric',
            'shopping_cart.tax_value' => 'required|numeric',
            'buyer' => 'required|array',
            'buyer.full_name' => 'required|string',
            'buyer.email_address' => 'required|email',
            'buyer.contact_phone' => 'required|string',
            'buyer.dni_number' => 'required|string',
            'shipping_address' => 'required|array',
            'shipping_address.street1' => 'required|string',
            'shipping_address.street2' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.country' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'payer' => 'required|array',
            'payer.email_address' => 'required|string',
            'payer.full_name' => 'required|string',
            'payer.contact_phone' => 'required|string',
            'payer.dni_number' => 'required|string',
            'billing_address' => 'required|array',
            'billing_address.street1' => 'required|string',
            'billing_address.street2' => 'required|string',
            'billing_address.city' => 'required|string',
            'billing_address.country' => 'required|string',
            'billing_address.state' => 'required|string',
            'billing_address.postal_code' => 'required|string',
            'billing_address.phone' => 'required|string',
            'ip' => 'required|string',
            'user_agent' => 'required|string',
            'type' => 'required|string',
            'url' => 'required|string'
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
