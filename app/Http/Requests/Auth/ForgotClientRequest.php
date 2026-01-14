<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Mews\Captcha\Facades\Captcha;

class ForgotClientRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
            'captcha_key' => 'required',
            'captcha' => 'required|captcha_api:'. request('captcha_key')
        ];
    }

    public function authorize()
    {
        return true;
    }
}
