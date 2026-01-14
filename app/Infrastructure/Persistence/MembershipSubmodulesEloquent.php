<?php

namespace App\Infrastructure\Persistence;

use App\Helpers\MembershipCalculateHelper;
use App\Infrastructure\Services\UtilsService;
use App\Models\Company;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use Carbon\Carbon;

class MembershipSubmodulesEloquent
{

    private $model;
    private $company;
    private $membershipModules;
    private $utilsService;


    public function __construct()
    {
        $this->model = new MembershipSubModule();
        $this->company = new Company();
        $this->membershipModules = new MembershipHasModules();
        $this->utilsService = new UtilsService();
    }

    public function storeMembershipSubmodules(array $data, string $moduleId,array $allModules, string $membershipModulesId)
    {
        $currentModule = collect($allModules)->first(function ($object) use ($membershipModulesId) {
            return $object['id'] === (int) $membershipModulesId;
        });
        collect($data)->each(function ($item) use($moduleId, $allModules, $membershipModulesId, $currentModule) {
            $quantity = 0;
            $name = null;
            if (in_array($item['id'], MembershipHasModules::SUBMODULES_INVOICE_IDS)) {
                $name = MembershipHasModules::NAME_INVOICE_PLAN;
                $quantity = collect($currentModule['sub_modules'])->first(function ($object) use ($item) {
                    return $item["id"] ===  $object["id"];
                })['quantity'];
            }

            if($currentModule['id'] == MembershipHasModules::MODULE_WEB_SITE) {
                $name = $currentModule["name"] . ' - ';
            }

            $module = collect($currentModule['sub_modules'])->first(function ($object) use ($item) {
                return $item["id"] === $object["id"];
            });

            $months = isset($item['expiration_date']) ? $item['expiration_date'] : null;
            $price = (in_array($item['id'], $this->model::SUB_MODULES_INVOICES) ? $module['base_price'] : ($module ? ( $months == 12 ? $module["price_year"] : $module["price_semester"] ) : null));
            $price_old = (in_array($item['id'], $this->model::SUB_MODULES_INVOICES) ? $module['base_price'] : ($module ? ($months == 12 ? ($price + $module['total_discount']): $price) : 0));

            $this->model::create([
                'membership_has_modules_id' => $moduleId,
                'sub_module_id' => $item['id'],
                'is_active' => false,
                'total_invoices' => $quantity,
                'remaining_invoices' => $quantity,
                'expiration_date' => isset($item['expiration_date']) ? Carbon::now()->addMonths($item['expiration_date'])->toDateString(): Carbon::now()->addMonths($item['expiration_date'])->toDateString(),
                'price' => $price,
                'price_old' => $price_old,
                'months' =>  $months,
                'name' => $module ? $name.$module["name"] : null,
                'discount' => 0
            ]);
        });
    }

    /**
     * Active subModules of membership
     * @param object $module
     * @param object $membership
     * @return void
     */
    public function activeSubModulesByModuleId(object $module, object $membership): void
    {
        $subModules = $this->model::where('membership_has_modules_id', $module->id)->get();
        collect($subModules)->each(function ($item) use ($module, $membership){
            $subModule = $this->model::find($item->id);
            $subModule->is_active = true;
            $subModule->is_frequent_payment = $module->membership_modules_id != MembershipHasModules::MODULE_INVOICE_ID;
            $subModule->save();
            if($module->membership_modules_id == MembershipHasModules::MODULE_INVOICE_ID) {
                $countModulesMembership = $membership->modules->count() - count($this->membershipModules::FREE_MODULES);
                $membership->is_frequent_payment = $countModulesMembership > 1;
                $membership->save();
                $company = $this->company::find($membership->company_id);
                $freeInvoices = ($company->invoices_available == 0) ? 15 : 0 ;
                $subModule = $this->utilsService->getSubModulesById([$subModule->sub_module_id]);
                $company->save();
            }
        });
    }

    /**
     * upgrade the website membership, basic to premium
     *
     * @param string $company_id uuid
     * @param string $membership_id uuid
     *
     * @return void
     */
    public function upgradeWebSiteMembership(string $company_id, string $membership_id): void
    {
        $subModulesWebsite = MembershipCalculateHelper::getSubModulesWebSiteMembership($company_id);

        $this->model->membership_has_modules_id = $subModulesWebsite->first()->membership_has_modules_id;
        $this->model->sub_module_id = $this->model::SUB_MODULE_WEBSITE_SHOP;
        $this->model->is_active = true;
        $this->model->save();


        $this->model::whereHas('membershipHasModule.membership', function($query) use ($membership_id){
            return $query->where('id', $membership_id);
        })->where('sub_module_id', $this->model::SUB_MODULE_WEBSITE_SHOP)->update(['is_active' => false]);
    }

}
