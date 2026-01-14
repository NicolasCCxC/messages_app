<?php

namespace App\Http\Resources\Company;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FiscalResposibilityCollection extends ResourceCollection
{

    private $data;

    public function using($value)
    {
        $this->data = collect($value['utils']['fiscal_responsibilities'] ?? []);
        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (FiscalResposibility $resource) use ($request) {
            return $resource->using($this->data)->toArray($request);
        })->all();
    }
}
