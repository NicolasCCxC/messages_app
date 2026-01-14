<?php

namespace App\Http\Requests\CompanyForeignExchange;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'foreign_exchange_id' => [
                'required',
                Rule::unique('companies_foreign_exchange')
                       ->where('company_id', $this->company_id)
            ],
            'company_id' => 'required|exists:App\Models\Company,id',
            'is_active' => 'required|boolean',
        ];
    }
}
