<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillingClientRequest extends FormRequest
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
            'document_number' => 'required|string',
            'document_type' => 'required|uuid',
            'email' => 'nullable|email|max:255',
            'name' => 'required|max:255',
            'company_id' => 'required|exists:App\Models\Company,id',
            'isPurchaseOrder' => 'required'
        ];
    }
}
