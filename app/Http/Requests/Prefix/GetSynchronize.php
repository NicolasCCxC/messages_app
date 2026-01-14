<?php

namespace App\Http\Requests\Prefix;

use Illuminate\Foundation\Http\FormRequest;

class GetSynchronize extends FormRequest
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
            'supplier_nit' => 'required'
        ];
    }
}
