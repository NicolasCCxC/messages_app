<?php

namespace App\Http\Resources;

use App\Infrastructure\Persistence\UserEloquent;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => PermissionResource::collection(UserEloquent::roleSpecific($this->permissions))
        ];
    }
}
