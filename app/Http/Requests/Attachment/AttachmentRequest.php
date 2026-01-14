<?php

namespace App\Http\Requests\Attachment;

use App\Http\Requests\GateRequest;

class AttachmentRequest extends GateRequest
{
    public function rules(): array
    {
        return [
            'company_id' => 'required|string|exists:App\Models\Company,id',
            'folder' => 'required|string',
            'is_bucket_detail_id' => 'boolean',
            'bucket_data' => 'string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
