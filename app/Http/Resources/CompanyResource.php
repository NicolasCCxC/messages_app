<?php

namespace App\Http\Resources;

use App\Http\Resources\Company\FiscalResposibilityCollection;
use App\Http\Resources\CompanyDevice\CompanyDeviceResource;
use App\Infrastructure\Persistence\CompanyForeignExchangeEloquent;
use App\Models\Company;
use App\Models\MembershipHasModules;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/** @mixin Company */
class CompanyResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $request->modulesUtils = $this->additional['modules'];

        // Find super Administrator
        $superAdmin = $this->users()->whereHas('role', function (Builder $query) {
            $query->where('name', Role::Main);
        })->first();

        $responseError = ['service' => Module::SECURITY, 'statusCode' => Response::HTTP_NOT_FOUND, 'message' => 'Super administrator email Not Found', 'errors' => []];
        // If it is not found the super administrator executes responseError
        $email = $superAdmin ? $superAdmin->email : abort( response()->json($responseError, Response::HTTP_NOT_FOUND) );

        $groupedObjects = collect($this->prefixes)->groupBy('type');
        $sortedObjects = $groupedObjects->map(function ($group) {
            return $group->sortByDesc('initial_validity');
        })->flatten();
        
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
            'make_web_page_type' => $this->make_web_page_type,
            'brand_established_service' => $this->brand_established_service,
            'accept_company_privacy' => $this->accept_company_privacy,
            'has_a_physical_store' => $this->has_a_physical_store,
            'has_e_commerce' => $this->has_e_commerce,
            'company_privacy_acceptation_date' => Carbon::parse($this->company_privacy_acceptation_date)->getTimeStamp(),
            'fiscal_responsibilities' => FiscalResposibilityCollection::make($this->fiscalResponsibilities)->using($this->additional),
            'whatsapp' => $this->whatsapp,
            'rut_bucket_id' => $this->rut_bucket_id,
            'is_billing_us' => $this->is_billing_us,
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
            'memberships' => MembershipCollectionResource::collection(
                $this->memberships()
                    ->with('modules.membershipSubmodules')->get()
            ),
            'tax_detail' => $this->tax_detail,
            'ciius' => $this->ciius()->orderBy('is_main','DESC')->get(),
            'params_from_utils' => $this->additional['utils']['tax_detail'] ?? [],
            'email' => $email,
            'logo_extension' => $this->additional['utils']['ciiu']['slogan'] ?? null,
            'modules' => ModulesCollection::make(
                MembershipHasModules::whereHas('membership.modules', function (Builder $builder) {
                    $builder->where('is_active',true);
                })
                    ->whereHas('membership.company', function (Builder $builder){
                        $builder->where('id', $this->id);
                    })
                    ->get()
                )
                ->using($this->additional['modules']),
            'active_memberships' => MembershipCollectionResource::collection(
                $this->memberships()
                    ->where('is_active', true)
                    ->with('modules.membershipSubmodules')->get()
            ),
            'no_active_memberships' => MembershipCollectionResource::collection(
                $this->memberships()
                    ->where('is_active', false)
                    ->where('payment_method', '!=', 'FREE')
                    ->with('modules.membershipSubmodules')->get()
            ),
            'prefixes' => $sortedObjects,
            'companies_foreign_exchange' => CompanyForeignExchangeEloquent::getCompanyForeignExchange($this->id),
            'company_devices' => CompanyDeviceResource::collection($this->companyDevices)
        ];
    }
}
