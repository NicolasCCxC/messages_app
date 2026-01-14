<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MembershipCollectionResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request)
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
            'invoice_pdf' => $this->invoice_pdf,
            'invoice_credit_note_id' => $this->invoice_credit_note_id,
            'invoice_credit_note_pdf' => $this->invoice_credit_note_pdf,
            'modules' => MembershipModulesCollectionResource::collection($this->modules),
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
        ];
    }
}
