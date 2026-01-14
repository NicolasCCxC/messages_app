<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Captcha\Facades\Captcha;

class ForgotRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|max:255'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
