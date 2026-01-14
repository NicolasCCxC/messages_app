<?php

namespace App\Http\Resources;

use App\Models\Membership;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class MembershipCollection extends ResourceCollection
{
    protected $modulesUtils;

    /**
     * Add external data to the resource
     *
     * @param Collection $value
     * @return $this
     */
    public function using(Collection $value): MembershipCollection
    {
        $this->modulesUtils = collect($value['modules']);
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

        $memberships = $this->collection->map(function (MembershipResource $membership) {
            return $membership->using(collect([
                'modules' => $this->modulesUtils,
            ]));
        });

        $total = $this->collection->reduce(function ($carry, $item) {
            return $carry + $item->resource->price;
        },0);

        return [
            'memberships' => $memberships,
            'price' => $total
        ];
    }
}
