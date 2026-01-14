<?php

namespace App\Http\Resources\Login;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            "document_number"=> $this->document_number,
            "document_type"=> $this->document_type,
            "last_login" => Carbon::parse($this->last_login)->getTimestamp(),
            "created_at" => Carbon::parse($this->created_at)->getTimestamp(),
            "updated_at" => Carbon::parse($this->updated_at)->getTimestamp(),
            "deleted_at" => Carbon::parse($this->deleted_at)->getTimestamp()
        ];
    }
}
