<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class ValidateAccessFreeDocumentsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "number_employees" => 'required|number',
            "total_revenue" => 'required|number',
            "is_validation" => 'required|boolean',
        ];
    }
}