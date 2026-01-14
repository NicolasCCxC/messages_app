<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|string|max:255',
            'name' => 'required|string|max:60',
            'document_type' => 'required|string',
            'foreign_exchange_id' => 'string',
            'foreign_exchange_code' => 'string',
            'document_number' => 'required|string|max:10',
            'company_representative_name' => 'required|string|max:60',
            'ciiu_id' => 'nullable|numeric|min:0',
            'ciiu_code' => 'nullable|string|max:4',
            'phone' => 'nullable|numeric|min:0',
            'country_id' => 'nullable|numeric|min:0',
            'country_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|numeric|min:0',
            'department_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|numeric|min:0',
            'city_name' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'address' => 'nullable|string|max:100',
            'domain' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|numeric|min:0',
            'has_a_physical_store' => 'boolean',
            'has_e_commerce' => 'boolean',
            'is_billing_us' => 'boolean',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
