<?php

namespace App\Http\Requests\Prefix;

use App\Models\Prefix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrefixTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.type' => ['required', Rule::in(Prefix::TYPE)],
            '*.contingency' => ['required', 'boolean'],
            '*.resolution_id' => ['required', 'uuid', Rule::exists('prefixes', 'id')],
        ];
    }
}
