<?php

namespace App\Helpers;

use App\Models\MembershipHasModules;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Role;

/** @mixin Company */
class CompaniesAdministrationHelper
{
    /**
     * @param Request $request
     * @return array
     */
    public static function companiesAdministrationDataReform($company, $modules, $utils): array
    {
        $personTypeMap = [
            "LEGAL_PERSON" => "Persona jurídica",
            "NATURAL_PERSON" => "Persona natural",
            "NATURAL_PERSON_MERCHANT" => 'Persona natural comerciante'
        ];

        $modulesResponse = [];
        $dateLastPlanPurchased = null;
        $superAdmin = $company->users()
        ->whereHas('role', fn (Builder $query) => $query->where('name', Role::Main))
        ->first();

        collect($company->memberships)->map(function ($membership) use (&$modulesResponse, &$dateLastPlanPurchased, $modules) {
            $date = $membership->initial_date;
            $formattedDate = is_numeric($date) && (int)$date == $date && $date >= 0  ? date('Y-m-d', $date) : $date;
            $dateLastPlanPurchased = $dateLastPlanPurchased
            ? (Carbon::parse($formattedDate)->isAfter($dateLastPlanPurchased) ? $formattedDate : $dateLastPlanPurchased)
            : $formattedDate;
            collect($membership->modules)->map(function ($module) use ($membership, &$modulesResponse, $modules) {
                $currentModule = collect($modules)->first(function ($object) use($module) {
                    return $object['id'] === (int) $module->membership_modules_id;
                });
                foreach ($module->membershipSubmodules as  $membershipSubModule) {
                    if($membershipSubModule){
                        $modulesResponse[] = [
                            'plan' => $membershipSubModule["name"],
                            'is_active' => $module->is_active ?? false,
                            'purchase_date_plan' => $membership->initial_date ?? null,
                            'plan_expiration_date' => $module->expiration_date ?? null,
                            'documents_processed' => $module->membership_modules_id === MembershipHasModules::MODULE_INVOICE_ID 
                                                    ? $membershipSubModule["total_invoices"] - $membershipSubModule["remaining_invoices"] : "N/A",
                        ];
                    } else {
                        $modulesResponse[] = [
                            'plan' => $module->name,
                            'is_active' => $module->is_active ?? false,
                            'purchase_date_plan' => $membership->initial_date ?? null,
                            'plan_expiration_date' => $module->expiration_date ?? null,
                            'documents_processed' => "N/A",
                        ];
                    }
                }
            });
        });
        
        return [
            'id' => $company->id,
            'name' => $company->name,
            'document_type' => collect($utils['document_types'])
                ->firstWhere('id', $company->document_type)['name'] ?? $company->document_type,
            'document_number' => $company->document_number,
            'person_type' => $personTypeMap[$company->person_type] ?? $company->person_type,
            'ciius' => $company->ciius()->orderBy('is_main', 'DESC')->pluck('code')->implode(','),
            'email' => isset($superAdmin->email) ? $superAdmin->email : "",
            'address' => $company->address,
            'phone' => $company->phone,
            'domain' => !empty($company->domain) ? $company->domain : 'N/A',
            'last_login' => isset($superAdmin->last_login) ? Carbon::parse($superAdmin->last_login)->format('Y-m-d') : "",
            'last_plan_purchased' => $dateLastPlanPurchased,
            'modules' => Collect($modulesResponse)->sortByDesc("purchase_date_plan")->values()
        ];
    }
}
