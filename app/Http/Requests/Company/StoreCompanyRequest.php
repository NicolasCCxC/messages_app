<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:200',
            'document_type' => 'required|string',
            'foreign_exchange_id' => 'nullable|string',
            'foreign_exchange_code' => 'nullable|string',
            'document_number' => 'required|string|max:13',
            'company_representative_name' => 'nullable|string|max:60',
            'phone' => 'nullable|numeric|min:0',
            'country_id' => 'nullable|numeric|min:0',
            'country_name' => 'nullable|string|max:255',
            'department_id' => 'nullable|numeric|min:0',
            'department_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|numeric|min:0',
            'city_name' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string',
            'address' => 'nullable|string|max:100',
            'accept_company_privacy' => 'required|boolean',
            'has_a_physical_store' => 'boolean',
            'has_e_commerce' => 'boolean',
            'company_privacy_acceptation_date' => 'required|integer',
            'whatsapp' => 'nullable|numeric|min:0',
            'ciius' => 'array',
            'ciius.*name' => 'required|string',
            'ciius.*code' => 'required|integer',
            'ciius.*ciiu_id' => 'required',
            'tax_detail' => 'nullable|integer',
            'companies_foreign_exchange' => 'array',
            'companies_foreign_exchange.*.foreign_exchange_id' => 'nullable|uuid',
            'companies_foreign_exchange.*.is_active' => 'nullable|boolean'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
