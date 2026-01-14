<?php

namespace App\Http\Resources\CompanyForeignExchange;

use Illuminate\Http\Resources\Json\JsonResource;

class ForeignExchange extends JsonResource
{
    private $data;

    /**
     * Add external data to the resource
     *
     * @param Collection $value
     * @return $this
     */
    public function using($value)
    {
        $this->data = collect($value);
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(
            ['is_active' => $this->is_active],
            $this->data->firstWhere('id',$this->foreign_exchange_id) ?? []
        );
    }
}
