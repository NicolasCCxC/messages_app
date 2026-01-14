<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoliticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'type' => $this->type,
            'url' => $this->url ?? 'not found',
            'name' => $this->name ?? 'not found'
        ];
    }
}
