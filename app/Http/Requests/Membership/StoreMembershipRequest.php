<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
{
    public function rules()
    {
        return [
            'is_immediate_purchase' => 'required|boolean',
            'users_quantity' => 'required|integer',
            'pages_quantity' => 'required|integer',
            'company_id' => 'required|uuid|max:255',
            'modules' => 'array',
            'modules.*.id' => 'integer',
            'additional_customer_data' => 'required|array',
            'payu_data' => 'array',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
