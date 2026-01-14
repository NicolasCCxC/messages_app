<?php

namespace App\Http\Requests\Prefix;

use App\Models\Prefix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotesRequest extends FormRequest
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
            '*.company_id' => 'required|exists:App\Models\Company,id',
            '*.type' => ['required', Rule::in([Prefix::DEBIT_NOTE, Prefix::CREDIT_NOTE, Prefix::ADJUSTMENT_NOTE])],
            '*.prefix' => 'required'
        ];
    }
}
