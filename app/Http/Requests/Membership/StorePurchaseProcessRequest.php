<?php

namespace App\Http\Requests\Membership;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'modules' => 'required|array',
            'modules.*.id' => 'required|integer',
            'modules.*.sub_modules' => 'nullable|array',
            'modules.*.sub_modules.*.id' => 'required|integer',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'modules.required' => 'The modules array is required.',
            'modules.*.id.required' => 'Each module must have an id.',
            'modules.*.id.integer' => 'Module id must be an integer.',
            'modules.*.sub_modules.*.id.required' => 'Each sub-module must have an id.',
            'modules.*.sub_modules.*.id.integer' => 'Sub-module id must be an integer.',
        ];
    }
}
