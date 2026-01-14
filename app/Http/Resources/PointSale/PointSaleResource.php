<?php

namespace App\Http\Resources\PointSale;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class PointSaleResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'physical_store_id' => $this->physical_store_id,
            'name' => $this->name,
            'contact_link' => $this->contact_link,
        ];
    }
}
