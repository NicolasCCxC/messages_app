<?php

namespace App\Http\Requests\Prefix;

use Illuminate\Foundation\Http\FormRequest;
use phpDocumentor\Reflection\PseudoTypes\True_;

class StoreRequest extends FormRequest
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
            '*.resolution_number' => 'required|integer',
            '*.type' => 'required',
            '*.prefix' => 'required',
            '*.initial_validity' => 'required|date',
            '*.final_validity' => 'required|date',
            '*.final_authorization_range' => 'required|integer',
            '*.initial_authorization_range' => 'required|integer',
            '*.physical_store' => 'required|boolean',
            '*.website' => 'required|boolean',
            '*.contingency' => 'required|boolean',
            '*.resolution_technical_key' => 'required_if:contingency,==,false'
        ];
    }
}
