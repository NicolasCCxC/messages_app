<?php

namespace App\Http\Requests\PayTransaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJsonPseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transaction_id' => 'required|uuid|exists:pay_transactions,transaction_id',
            'json_pse_url_response' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}