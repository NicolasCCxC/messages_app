<?php

namespace App\Http\Resources;

use App\Infrastructure\Persistence\UserEloquent;
use App\Models\Company;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $userEloquen = new UserEloquent();
        return [
            "id"=> $this->id,
            "email"=> $this->email,
            "name" => $this->name,
            "type"=> $this->type,
            "document_number"=> $this->document_number,
            "document_type"=> $this->document_type,
            "company"=> $this->company,
            "roles" => RoleResource::collection($this->role),
            "last_login" => $this->last_login,
            "accept_data_policy" => $this->accept_data_policy,
            "user_privacy_acceptation_date" => Carbon::parse($this->user_privacy_acceptation_date)->getTimestamp(),
            "created_at" => Carbon::parse($this->created_at)->getTimestamp(),
            "updated_at" => Carbon::parse($this->updated_at)->getTimestamp(),
            "deleted_at" => Carbon::parse($this->deleted_at)->getTimestamp()
        ];
    }
}
