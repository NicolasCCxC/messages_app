<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/\d/',
                'regex:/[^A-Za-z0-9]/'
            ],
            'email' => 'required|email|max:255',
            'name' => 'max:255',
            'company_id'=> 'required|uuid|exists:App\Models\Company,id',
            'captcha_key' => 'required',
            'captcha' => 'required|captcha_api:'. request('captcha_key')
        ];
    }
}
