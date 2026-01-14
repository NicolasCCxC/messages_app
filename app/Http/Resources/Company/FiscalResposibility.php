<?php

namespace App\Http\Resources\Company;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class FiscalResposibility extends JsonResource
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
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'number_resolution' => $this->number_resolution,
            'date' => $this->date,
            'withholdings' => $this->code_fiscal_responsibility == "2" ? $this->withholdings : []
        ],
            $this->data->firstWhere('id',$this->code_fiscal_responsibility) ?? []);
    }
}
