<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Membership */
class MembershipResourceCollection extends ResourceCollection
{

    protected $modules;

    /**
     * Add external data to the resource
     *
     * @param $value
     * @return MembershipResourceCollection
     */
    public function using($value): MembershipResourceCollection
    {
        $this->modules = $value['modules'];
        return $this;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (MembershipResource $membership) use ($request) {
            return $membership->using(collect([
                'modules' => $this->modules,
            ]))->toArray($request);
        })->all();
    }
}
