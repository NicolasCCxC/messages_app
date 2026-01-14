<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class PrefixResource extends JsonResource
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
            'resolution_number' => $this->resolution_number,
            'type' => $this->type,
            'prefix' => $this->prefix,
            'initial_validity' =>  Carbon::parse($this->initial_validity)->getTimestamp(),
            'final_validity' => Carbon::parse($this->final_validity)->getTimestamp(),
            'final_authorization_range' => $this->final_authorization_range,
            'initial_authorization_range' => $this->initial_authorization_range,
            'physical_store' => $this->physical_store,
            'website' => $this->website,
            'contingency' => $this->contingency,
            'supporting_document' => $this->supporting_document,
            'company_id' => $this->company_id,
            'resolution_technical_key' => $this->resolution_technical_key
        ];
    }
}
