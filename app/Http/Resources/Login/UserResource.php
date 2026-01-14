<?php

namespace App\Http\Resources\Login;

use App\Http\Resources\RoleResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "email"=> $this->email,
            "name" => $this->name,
            "type"=> $this->type,
            "document_number"=> $this->document_number,
            "company_name" => $this->company->name ?? null,
            "document_type"=> $this->document_type,
            "company_id"=> $this->company_id,
            "roles" => RoleResource::collection($this->role),
            "last_login" => Carbon::parse($this->last_login)->getTimestamp(),
            "accept_data_policy" => $this->accept_data_policy,
            "accept_terms_conditions" => $this->accept_terms_conditions,
            "is_first_login" => $this->is_first_login,
            "user_privacy_acceptation_date" => Carbon::parse($this->user_privacy_acceptation_date)->getTimestamp(),
            "user_terms_conditions_acceptation_date" => Carbon::parse($this->user_terms_conditions_acceptation_date)->getTimestamp(),
            "created_at" => Carbon::parse($this->created_at)->getTimestamp(),
            "updated_at" => Carbon::parse($this->updated_at)->getTimestamp(),
            "deleted_at" => Carbon::parse($this->deleted_at)->getTimestamp()
        ];
    }
}
