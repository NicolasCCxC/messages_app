<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Company;

class StoreUserRequest extends FormRequest
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
            'name' => 'max:200',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/\d/',
                'regex:/[^A-Za-z0-9]/'
            ],
            'email' => 'required|email|unique:users,email|max:255',
            'phone' => 'nullable|numeric|min:0',
            'company_representative_name' => 'nullable|string|max:60',
            'document_type' => [
                'string',
                Rule::unique('companies')->where(function ($query) {
                    return $query->where('document_number', $this->nit);
                })
            ],
            'company_id'=> 'required_without:accept_policy|uuid',
            'accept_policy' => 'required_without:company_id|boolean',
            'accept_terms' => 'boolean',
            "roles" => "required_with:company_id|array",
            'roles.*.name' => 'required_with:roles',
            "roles.*.permissions" => "array",
            'roles.*.permissions.*.name' => 'required_with:roles',
            'roles.*.permissions.*.description' => 'required_with:roles',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $existingCompany = Company::where('document_number', $this->nit)->first();

            if ($existingCompany) {
                $validator->errors()->add('document_type', 'The document type has already been taken.');
            }
        });
    }
}
