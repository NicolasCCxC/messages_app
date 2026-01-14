<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyBillingRequest extends FormRequest
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
            'fiscal_responsibilities' => 'required|array',
            'person_type' => 'required|string|max:60',
            'tax_detail' => 'required|integer',
            'companies_foreign_exchange' => 'array',
            'companies_foreign_exchange.*.foreign_exchange_id' => 'nullable|uuid',
            'companies_foreign_exchange.*.is_active' => 'nullable|boolean'
        ];
    }
}
