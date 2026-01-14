<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Infrastructure\Formulation\MembershipHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountCreatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $documents = MembershipHelper::getAllDocumentTypes();
        $nameTypeDocument = isset($company->document_type) ? collect($documents)->where('id',$company->document_type)->first() : null;

        return [
            "company_id" => $this->id,
            "name" => $this->name,
            "document_number"=> $this->document_number,
            "document_type"=> $nameTypeDocument,
            "document_type_id"=> $this->document_type,
            "phone"=> $this->phone,
            "company_representative_name"=> $this->company_representative_name,
        ];
    }
}
