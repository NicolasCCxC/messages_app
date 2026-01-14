<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class MembershipInvoiceResource extends JsonResource
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => 'Membresía',
            'units' => 1,
            'unit_value' => $this->resource['total'],
            'sale' => 0,
            'discount' => 0,
            'sale_cost' => 0,
            'iva' => 5
        ];
    }
}
