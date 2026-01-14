<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyAccountCreated extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:60',
            'company_representative_name' => 'nullable|string|max:120',
            'phone' => 'nullable|numeric|min:0',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
