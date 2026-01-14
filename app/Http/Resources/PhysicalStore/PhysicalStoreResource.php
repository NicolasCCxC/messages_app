<?php

namespace App\Http\Resources\PhysicalStore;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class PhysicalStoreResource extends JsonResource
{

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'address' => $this->address,
            'point_sales' => $this->pointSales,
            'country_id' => $this->country_id,
            'country_name' => $this->country_name,
            'department_id' => $this->department_id,
            'department_name' => $this->department_name,
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'phone' => $this->phone
        ];
    }
}
