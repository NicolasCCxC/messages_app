<?php

namespace App\Http\Resources;

use App\Http\Resources\Company\FiscalResposibilityCollection;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin Company */
class CompanyWithEmailResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'person_type' => $this->person_type,
            'document_type' => $this->document_type,
            'foreign_exchange_id' => $this->foreign_exchange_id,
            'foreign_exchange_code' => $this->foreign_exchange_code,
            'document_number' => $this->document_number,
            'company_representative_name' => $this->company_representative_name,
            'phone' => $this->phone,
            'country_id' => $this->country_id,
            'country_name' => $this->country_name,
            'department_id' => $this->department_id,
            'department_name' => $this->department_name,
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'domain' => $this->domain,
            'email' => $this->users->first()->email,
            'make_web_page_type' => $this->make_web_page_type,
            'brand_established_service' => $this->brand_established_service,
            'accept_company_privacy' => $this->accept_company_privacy,
            'has_a_physical_store' => $this->has_a_physical_store,
            'has_e_commerce' => $this->has_e_commerce,
            'company_privacy_acceptation_date' => Carbon::parse($this->company_privacy_acceptation_date)->getTimeStamp(),
            'fiscal_responsibilities' => FiscalResposibilityCollection::make($this->fiscalResponsibilities)->using($this->additional),
            'whatsapp' => $this->whatsapp,
            'rut_bucket_id' => $this->rut_bucket_id,
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
            'tax_detail' => $this->tax_detail,
            'ciius' => $this->ciius()->orderBy('is_main','DESC')->get(),
            'tax_detail_name' => $this->additional['utils']['tax_detail']['name'] ?? [],
            'document_type_name' => $this->additional['utils']['document_type']['name'] ?? [],
            'is_billing_us' => $this->is_billing_us
        ];
    }
}
