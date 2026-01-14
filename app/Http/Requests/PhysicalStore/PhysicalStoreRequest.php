<?php

namespace App\Http\Requests\PhysicalStore;

use Illuminate\Foundation\Http\FormRequest;

class PhysicalStoreRequest extends FormRequest
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
            '*.id' => '',
            '*.company_id' => 'required|uuid|exists:App\Models\Company,id',
            '*.name' => 'required|max:255',
            '*.address' => 'required',
            '*.point_sales' => 'array',
            '*.point_sales.*.name' => '',
            '*.point_sales.*.contact_link' => '',
            '*.phone' => 'required|numeric|min:0',
            '*.country_id' => 'required|numeric|min:0',
            '*.country_name' => 'required|string|max:255',
            '*.department_id' => 'nullable',
            '*.department_name' => 'required|string|max:255',
            '*.city_id' => 'nullable',
            '*.city_name' => 'required|string|max:255',
        ];
    }
}
