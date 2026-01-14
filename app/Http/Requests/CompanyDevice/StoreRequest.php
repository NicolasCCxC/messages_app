<?php

namespace App\Http\Requests\CompanyDevice;

use Illuminate\Foundation\Http\FormRequest;

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
            'company_id' => 'required|uuid|exists:App\Models\Company,id',
            'devices' => 'required|array',
            'devices.*.name' => 'required|string'
        ];
    }
}
