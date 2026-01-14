<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\MembershipHasModules;

class MembershipPurchaseProcessResource extends JsonResource
{
    public function toArray($request)
    {
        $modules = [];
        $modulesUtils = $this->additional['modules'];
        $countModules = $this->purchaseProcessDetails->where('module_id', '!=', 5)->groupBy('module_id')->count();
        $this->purchaseProcessDetails
            ->groupBy('module_id')
            ->map(function ($details, $moduleId) use ($modulesUtils, &$modules, $countModules) {
                $moduleInfo = collect($modulesUtils)->firstWhere('id', $moduleId);
                $subModulesInfo = $moduleInfo['sub_modules'] ?? [];
                $subModules = [];
                if(count($subModulesInfo) != 0){
                    $details->map(function ($detail) use ($subModulesInfo, &$modules) {
                        $subModuleData = collect($subModulesInfo)->firstWhere('id', $detail->sub_module_id);
                        if($subModuleData['modules_id'] == MembershipHasModules::MODULE_INVOICE_ID){
                            $subModuleData['price_year'] = $subModuleData['base_price'];
                        }
                        $modules = collect($modules)->merge([ 
                                $detail->sub_module_id => array_merge($subModuleData, [
                                    'details_id' => $detail->id,
                                    'name' => $subModuleData['name'],
                                    'price_year' => $subModuleData['price_year']
                                ])
                            ]);
                    });
                }

                if(empty($subModulesInfo)){
                    if($moduleInfo['id'] == MembershipHasModules::PLANNING_ORGANIZATION_ID && $countModules > 1){
                        $moduleInfo['price_year'] = 0;
                    }
                    $modules = collect($modules)->merge([
                        $moduleId => array_merge($moduleInfo, [
                            'details_id' => $details->first()->id,
                        ])
                    ]);
                }
                
            })->values();

        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'price' => $this->price,
            'is_payment' => $this->is_payment,
            'modules' => $modules->keyBy('id'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
