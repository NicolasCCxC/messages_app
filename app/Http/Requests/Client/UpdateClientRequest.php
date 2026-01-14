<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Client;

class UpdateClientRequest extends FormRequest
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
    public function rules() : array
    {
//        $client = Client::find($this->id);
        return [
            'id' => 'required',
            'email' => 'email|max:255',
            'password' => 'min:8|confirmed',
            'document_number' => 'max:255',
            'document_type' => 'max:255',
        ];
    }
}
