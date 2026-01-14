<?php

namespace App\Http\Requests\Prefix;

use Illuminate\Foundation\Http\FormRequest;

class GetPrefixRequest extends FormRequest
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
            'company_id' => 'required|exists:App\Models\Company,id',
            'prefix_id' => 'required|uuid',
        ];
    }
}
