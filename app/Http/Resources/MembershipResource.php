<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/** @mixin \App\Models\Membership */
class MembershipResource extends JsonResource
{

    protected $modulesUtils;

    /**
     * Add external data to the resource
     *
     * @param Collection $value
     * @return $this
     */
    public function using(Collection $value): MembershipResource
    {
        $this->modulesUtils = collect($value['modules']);
        return $this;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'purchase_date' => $this->purchase_date != null ? Carbon::parse($this->purchase_date)->getTimestamp() : $this->purchase_date,
            'initial_date' => $this->initial_date != null ? Carbon::parse($this->initial_date)->getTimestamp() : $this->initial_date,
            'expiration_date' => $this->expiration_date != null ? Carbon::parse($this->expiration_date)->getTimestamp() : $this->expiration_date,
            'is_active' => $this->is_active,
            'company_id' => $this->company_id,
            'is_first_payment' => $this->is_first_payment,
            'is_frequent_payment' => $this->is_frequent_payment,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'total' => $this->price,
            'modules' => MembershipModulesCollection::make($this->modules)->using($this->modulesUtils),
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
        ];
    }
}
