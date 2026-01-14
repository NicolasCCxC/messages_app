<?php

namespace App\Http\Resources\Company;

use App\Models\Company;
use App\Models\Module;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $superAdmin = $this->users()->whereHas('role', function (Builder $query) {
            $query->where('name', Role::Main);
        })->first();

        $responseError = ['service' => Module::SECURITY, 'statusCode' => Response::HTTP_NOT_FOUND, 'message' => 'Super administrator email Not Found', 'errors' => []];
        $email = $superAdmin ? $superAdmin->email : abort(response()->json($responseError, Response::HTTP_NOT_FOUND));

        $groupedObjects = collect($this->prefixes)->groupBy('type');
        $sortedObjects = $groupedObjects->map(function ($group) {
            return $group->sortByDesc('initial_validity');
        })->flatten();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'person_type' => $this->person_type,
            'phone' => $this->phone,
            'person_type_id' => isset($this->person_type) ? Company::PERSON_TYPES_ID[$this->person_type] : null,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'company_representative_name' => $this->company_representative_name,
            'country_id' => $this->country_id,
            'country_name' => $this->country_name,
            'department_id' => $this->department_id,
            'department_name' => $this->department_name,
            'city_id' => $this->city_id,
            'city_name' => $this->city_name,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'domain' => $this->domain,
            'attachments' => $this->attachments,
            'fiscal_responsibilities' => FiscalResposibilityCollection::make($this->fiscalResponsibilities)->using($this->additional),
            'params_from_utils' => $this->additional['utils']['tax_detail'] ?? [],
            'email' => $email,
            'prefixes' => $sortedObjects,
            'company_taxes' => $this->companyTaxes,
            'document_type_name' => $this->additional['utils']['document_type']['name'] ?? [],
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'ciius' => $this->ciius()->orderBy('is_main', 'DESC')->get(),
        ];
    }
}