<?php

namespace App\Infrastructure\Persistence;


use App\Models\MembershipHasModules;
use Carbon\Carbon;


class MembershipDetailEloquent
{
    /**
     * @var MembershipSubmodulesEloquent
     */
    private $membershipSubmodulesEloquent;

    private $model;

    public function __construct()
    {
        $this->model = new MembershipHasModules();
        $this->membershipSubmodulesEloquent = new MembershipSubmodulesEloquent();
    }

    /**
     * Save Modules of Membership
     *
     * @param array $data
     * @param object $membership
     * @return void
     */
    public function storeMembershipHasModules(array $data, object $membership, $allModules): void
    {
        collect($data)->each(function ($item) use($membership, $allModules) {
            $moduleUtils = Collect($allModules)->first(function ($object) use ($item) {
                return $object["id"] ===  $item['id'];
            });

            $months = isset($item['expiration_date']) ? $item['expiration_date'] : null;
            $price = $moduleUtils ? ( $months == 12 ? $moduleUtils["price_year"] : $moduleUtils["price_semester"] ) : null;

            $module = $this->model::create([
                'membership_id' => $membership->id,
                'membership_modules_id' => $item['id'],
                'is_active' =>false,
                'percentage_discount' => 0,
                'expiration_date' => isset($item['sub_modules'][0]['expiration_date']) ? Carbon::now()->addMonths($item['sub_modules'][0]['expiration_date'])->toDateString(): Carbon::now()->addMonths($item['expiration_date'])->toDateString(),
                'price' => $price,
                'price_old' => ($moduleUtils ? ( $months == 12 ? ($price + $moduleUtils["total_discount"]) : $price ) : 0),
                'months' => $months,
                'name' => $moduleUtils ? $moduleUtils["name"] : null,
            ]);

            if(isset($item['sub_modules']) && count($item['sub_modules']) > 0)
                $this->membershipSubmodulesEloquent->storeMembershipSubmodules($item['sub_modules'], $module->id, $allModules, $item['id']);
        });
    }

    public function createFreeMembershipModule(array $data, object $membership, $allModules){
        $moduleUtils = Collect($allModules)->first(function ($object) use ($data) {
            return $object["id"] ===  $data['id'];
        });
        
        $exists = $this->model::where('membership_modules_id', $data['id'])
                ->where('membership_id', $membership->id)
                ->exists();
                
        if(!$exists){
            $this->model::create([
                'membership_id' => $membership->id,
                'membership_modules_id' => $data['id'],
                'is_active' => true,
                'percentage_discount' => 0,
                'expiration_date' =>  Carbon::now()->addMonths(MembershipHasModules::EXPIRATION_DATE_FREE_MODULES)->toDateString(),
                'price' => 0,
                'price_old' => 0,
                'months' => MembershipHasModules::EXPIRATION_DATE_FREE_MODULES,
                'name' => $moduleUtils ? $moduleUtils["name"] : null,
            ]);
        }
    }

    public function getMembershipsModules(string $membershipId)
    {
        return $this->model::where('membership_id', $membershipId)->get();
    }

    public function inactiveAllMembershipsModules($company_id)
    {
        $this->model::whereHas('membership', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        })->update(['is_active' => false]);
    }

    /**
     * Active modules of membership
     * @param object $membership
     * @return void
     */
    public function activeModulesByMembershipId(object $membership): void
    {
        $modules = $this->model::where('membership_id', $membership->id)->get();
        collect($modules)->each(function ($item) use($membership) {
            $module = $this->model::find($item->id);
            $module->is_active = true;
            $module->is_frequent_payment = $module->membership_modules_id != $this->model::MODULE_INVOICE_ID;
            $module->save();
            $this->membershipSubmodulesEloquent->activeSubModulesByModuleId($module, $membership);
        });
    }
}
