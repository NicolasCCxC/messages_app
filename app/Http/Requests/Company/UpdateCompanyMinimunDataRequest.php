<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyMinimunDataRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:60',
            'document_type' => 'required|string',
            'document_number' => 'required|string|max:10',
            'person_type' => 'required|in:' . implode(',', Company::PERSON_TYPES),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
