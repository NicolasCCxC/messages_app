<?php

namespace App\Http\Requests\Politic;

use Illuminate\Foundation\Http\FormRequest;

class PrivacyPurposesRequest extends FormRequest
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
            'id' => 'uuid|exists:privacy_purposes,id',
            'description' => 'required|min:5'
        ];
    }
}
