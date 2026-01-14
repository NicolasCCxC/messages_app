<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'resource' => 'required|string|',
            'method' => 'required|string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
