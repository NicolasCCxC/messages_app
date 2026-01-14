<?php

namespace App\Http\Resources;

use App\Infrastructure\Formulation\MembershipHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class MembershipModulesCollectionResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request)
    {
        if(!isset($request->modulesUtils))
            $request->modulesUtils = MembershipHelper::getAllMembershipModules()['modules'];

        return [
            'id' => $this->id,
            'name' => collect($request->modulesUtils)->firstWhere('id', $this->membership_modules_id)['name'],
            'price_year' => collect($request->modulesUtils)->firstWhere('id', $this->membership_modules_id)['price_year'],
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
            'percentage_discount' => $this->percentage_discount,
            'is_frequent_payment' => $this->is_frequent_payment,
            'sub_modules' => SubModulesResource::collection($this->membershipSubmodules),
        ];
    }
}
