<?php

namespace App\Helpers;

use App\Infrastructure\Persistence\MembershipDetailEloquent;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class MembershipCalculateHelper
{
    public static function validateIfCanPurchaseExtraPages($companyId, $modules): bool
    {
        $membershipCount = Membership::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('payment_status', Membership::PAYMENT_STATUS_APPROVED)
            ->whereHas('modules', function($query){
                return $query->where('membership_modules_id', MembershipHasModules::MODULE_WEB_SITE);
            })
            ->count();

        $modulesCount = collect($modules)->where('id', MembershipHasModules::MODULE_WEB_SITE)->count();

        return ($membershipCount + $modulesCount) == 0;
    }

    public static function percentageDiscountThirdModule($keyPositionModuleRequest, $company_id, $module_id)
    {
        $membershipModulesModel = new MembershipHasModules();
        $modulesToIgnore = collect(MembershipHasModules::FREE_MODULES)->pluck('id')->toArray();

        $modulesInDataBase = $membershipModulesModel::whereHas('membership', function($query) use ($company_id){
            return $query->where('company_id', $company_id)->where('payment_status', Membership::PAYMENT_STATUS_APPROVED);
        })
            ->whereNotIn('membership_modules_id', $modulesToIgnore)
            ->get()
            ->unique('membership_modules_id');

        $findModule = $modulesInDataBase->where('membership_modules_id', $module_id)->count();

        if($findModule > 0) return 1;

        $plusOne = ($modulesInDataBase->count() == 0) ? 1 : ($keyPositionModuleRequest == 0 ? 1 : 0);
        $positionModule = $modulesInDataBase->count() + ($keyPositionModuleRequest+$plusOne);
        if($positionModule == 3)
            return (1 - Membership::DISCOUNT_MODULE_MEMBERSHIP);

        return 1;
    }

    public static function validateIfIsFirstPaymentOrFreeMembership($company_id): bool
    {
        $membership = Membership::where('company_id', $company_id)
            ->where('is_first_payment', true)
            ->where('payment_status', Membership::PAYMENT_STATUS_APPROVED)
            ->get()
            ->count();

        return $membership == 0;
    }

    /**
     * validate if the company want upgrade the website membership
     *
     * @param string $company_id uuid
     * @param array $modules
     *
     * @return Bool
     */
    public static function validateIfIsUpgradeWebSite(string $company_id, array $modules): bool
    {
        $webSiteModule = collect($modules)->where('id', MembershipHasModules::MODULE_WEB_SITE)->first();
        if(!$webSiteModule) return false;
        $moduleWebSiteRequestCount = count($webSiteModule['sub_modules']);

        $subModulesWebsiteCount = self::getSubModulesWebSiteMembership($company_id)->count();

        return $subModulesWebsiteCount == 1 && $moduleWebSiteRequestCount == 2;
    }

    /**
     * get the active submodules website membership by company
     *
     * @param string $company_id uuid
     *
     * @return Collection
     */
    public static function getSubModulesWebSiteMembership(string $company_id): Collection
    {
        $membershipModulesModel = new MembershipHasModules();
        $membershipSubModules = new MembershipSubModule();

        return $membershipSubModules::whereHas('membershipHasModule.membership', function($query) use ($company_id){
            return $query->where('company_id', $company_id)
                ->where('payment_status', Membership::PAYMENT_STATUS_APPROVED)
                ->where('is_active', true)
                ->where('is_frequent_payment', true);
        })->whereHas('membershipHasModule', function($query) use ($membershipModulesModel){
            return $query->where('membership_modules_id', $membershipModulesModel::MODULE_WEB_SITE)->where('is_active', true);
        })->where('is_active', true)->get();
    }

    /**
     * This function is used to add a free user when the company get your first module
     * @return void
     */
    public static function addFreeUser(string $company_id = null): void
    {
        $companyId = auth()->user()->company_id ?? $company_id;
        $company = Company::find($companyId);
        $usersAvailable = $company->users_available;

        if($usersAvailable == 0) {
            $company->users_available = Membership::FREE_USERS;
            $company->save();
        }
    }

    /**
     * Validate if the purchase could be free
     * @param array $modules
     * @param int $pagesQuantity
     * @return bool
     */
    public static function validateIfCreateFreeMembership(array $modules, int $pagesQuantity): bool
    {
        $validate = true;
        if(collect($modules)->first()['id'] == MembershipHasModules::MODULE_INVOICE_ID)
            $validate = false;

        if(collect($modules)->first()['id'] == MembershipHasModules::MODULE_WEB_SITE && $pagesQuantity > 0)
            $validate = false;

        if(collect($modules)->first()['id'] == MembershipHasModules::MODULE_WEB_SITE){
            if(count(collect($modules)->first()['sub_modules']) > 1)
                $validate = false;
        }

        return $validate;
    }

    /**
     * Clean modules for not repeat purchase
     * @param array $modules
     * @param bool $isUpgradeWebSite
     * @return array
     */
    public static function cleanDataModules(array $modules, bool $isUpgradeWebSite): array
    {
        $membershipModulesModel = new MembershipHasModules();
        $modulesToIgnore = collect(MembershipHasModules::FREE_MODULES)->pluck('id')->toArray();
        $companyId = auth()->user()->company_id;

        $modulesDb = $membershipModulesModel::whereHas('membership', function($query) use ($companyId){
            return $query->where('company_id', $companyId)
                ->where('payment_status', Membership::PAYMENT_STATUS_APPROVED)
                ->where('is_active', true);
        })->whereNotIn('membership_modules_id', $modulesToIgnore)
            ->get()
            ->unique('membership_modules_id')
            ->pluck('membership_modules_id')
            ->toArray();

        return collect($modules)->filter(function($module) use ($modulesDb, $isUpgradeWebSite, $membershipModulesModel){
            $key = array_keys($modulesDb, $membershipModulesModel::MODULE_INVOICE_ID);
            if (count($key) > 0) unset($modulesDb[$key[0]]);
            if ($isUpgradeWebSite) unset($modulesDb[array_keys($modulesDb, $membershipModulesModel::MODULE_WEB_SITE)[0]]);

            return !in_array($module['id'], $modulesDb);
        })->values()->toArray();
    }

    public static function countValidModules($membershipsActives, $validIds = [2, 3, 16]) {
        $totalCount = 0;
        foreach ($membershipsActives as $membership) {
            if (isset($membership->modules) && $membership->modules instanceof \Illuminate\Database\Eloquent\Collection) {
                $filteredModules = $membership->modules->filter(function ($module) use ($validIds) {
                    return in_array($module->membership_modules_id, $validIds);
                });
                $totalCount += $filteredModules->count();
            }
        }
        return $totalCount;
    }
}
