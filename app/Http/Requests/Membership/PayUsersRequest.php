<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class PayUsersRequest extends FormRequest
{
    public function rules()
    {
        return [
            'company_id' => 'required|uuid|max:255',
            'users_quantity' => 'required|integer',
            'payu_data' => 'array',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
