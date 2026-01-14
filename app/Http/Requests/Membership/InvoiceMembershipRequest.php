<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceMembershipRequest extends FormRequest
{
    public function rules()
    {
        return [
            'membership_id' => 'required|string|max:255',
            'user_id' => 'required|string|max:255'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
