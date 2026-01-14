<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class PayWithOutTokenRequest extends FormRequest
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
            'transaction' => 'required|array',

            'transaction.order' => 'required|array',
            'transaction.order.description' => 'required|string',
            'transaction.order.notifyUrl' => 'required|string',
            'transaction.order.additionalValues' => 'required|array',
            'transaction.order.additionalValues.TX_VALUE' => 'required|array',
            'transaction.order.additionalValues.TX_VALUE.value' => 'required|numeric',
            'transaction.order.additionalValues.TX_VALUE.currency' => 'required|string',
            'transaction.order.additionalValues.TX_TAX' => 'required|array',
            'transaction.order.additionalValues.TX_TAX.value' => 'required|numeric',
            'transaction.order.additionalValues.TX_TAX.currency' => 'required|string',
            'transaction.order.additionalValues.TX_TAX_RETURN_BASE' => 'required|array',
            'transaction.order.additionalValues.TX_TAX_RETURN_BASE.value' => 'required|numeric',
            'transaction.order.additionalValues.TX_TAX_RETURN_BASE.currency' => 'required|string',

            'transaction.order.buyer' => 'required|array',
            'transaction.order.buyer.fullName' => 'required|string',
            'transaction.order.buyer.emailAddress' => 'required|email',
            'transaction.order.buyer.contactPhone' => 'required|string',
            'transaction.order.buyer.dniNumber' => 'required|string',
            'transaction.order.buyer.shippingAddress' => 'required|array',
            'transaction.order.buyer.shippingAddress.street1' => 'required|string',
            'transaction.order.buyer.shippingAddress.street2' => 'required|string',
            'transaction.order.buyer.shippingAddress.city' => 'required|string',
            'transaction.order.buyer.shippingAddress.country' => 'required|string',
            'transaction.order.buyer.shippingAddress.state' => 'required|string',
            'transaction.order.buyer.shippingAddress.postalCode' => 'required|string',
            'transaction.order.buyer.shippingAddress.phone' => 'required|string',

            'transaction.payer' => 'required|array',
            'transaction.payer.fullName' => 'required|string',
            'transaction.payer.emailAddress' => 'required|email',
            'transaction.payer.contactPhone' => 'required|string',
            'transaction.payer.dniNumber' => 'required|string',
            'transaction.payer.billingAddress' => 'required|array',
            'transaction.payer.billingAddress.street1' => 'required|string',
            'transaction.payer.billingAddress.street2' => 'required|string',
            'transaction.payer.billingAddress.city' => 'required|string',
            'transaction.payer.billingAddress.country' => 'required|string',
            'transaction.payer.billingAddress.state' => 'required|string',
            'transaction.payer.billingAddress.postalCode' => 'required|string',
            'transaction.payer.billingAddress.phone' => 'required|string',

            'transaction.creditCard' => 'required|array',
            'transaction.creditCard.number' => 'required|string',
            'transaction.creditCard.securityCode' => 'required|string',
            'transaction.creditCard.expirationDate' => 'required|string',
            'transaction.creditCard.name' => 'required|string',

            'transaction.paymentMethod' => 'required|string',
            'transaction.ipAddress' => 'required|string',
            'transaction.userAgent' => 'required|string',
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
