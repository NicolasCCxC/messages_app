<?php

namespace App\Http\Requests\Politic;

use Illuminate\Foundation\Http\FormRequest;

class StorePoliticsRequets extends FormRequest
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
            'return_policy' => 'mimetypes:application/pdf|max:5000',
            'right_of_withdrawal' => 'mimetypes:application/pdf|max:5000',
            'warranty_policy' => 'mimetypes:application/pdf|max:5000',
            'shipping_policy' => 'mimetypes:application/pdf|max:5000',
            'refund_policies' => 'mimetypes:application/pdf|max:5000',
            'terms_and_conditions' => 'mimetypes:application/pdf|max:5000',
            'data_privacy_policy' => 'mimetypes:application/pdf|max:5000',
        ];
    }
}
