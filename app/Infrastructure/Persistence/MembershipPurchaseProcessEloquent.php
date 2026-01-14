<?php

namespace App\Infrastructure\Persistence;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\MembershipPurchaseProcess;
use App\Models\MembershipPurchaseProcessDetail;
use App\Infrastructure\Formulation\MembershipHelper;
use App\Http\Resources\MembershipPurchaseProcessResource;

class MembershipPurchaseProcessEloquent
{
    /**
     * @var MembershipEloquent
     */
    private $membershipEloquent;

    /**
     * @var MembershipPurchaseProcess
     */
    private $model;

    /**
     * @var MembershipPurchaseProcessDetail
     */
    private $modelDetails;

    public function __construct()
    {
        $this->model = new MembershipPurchaseProcess();
        $this->modelDetails = new MembershipPurchaseProcessDetail();
        $this->membershipEloquent = new MembershipEloquent();
    }

    /**
     * Save Membership Purchase Process
     *
     * @param array $data
     * @param string $companyId
     * @return void
     */
    public function storeMembershipPurchaseProcess(array $data, string $companyId)
    {
        $data["users_quantity"] = 0;
        $allModulesUtils = MembershipHelper::getAllMembershipModules()['modules'];
        $price = $this->membershipEloquent->calculateTotalPriceMembership($data, $data['modules'], $allModulesUtils);
        $process = MembershipPurchaseProcess::where('company_id', $companyId)
        ->where('is_payment', false)
        ->first();

        if ($process) {
            $process->purchaseProcessDetails()->delete();
            $process->delete();
        } 

        $process = MembershipPurchaseProcess::create([
            'id' => Str::uuid(),
            'company_id' => $companyId,
            'price' => $price,
            'is_payment' => false
        ]);

        foreach ($data['modules'] as $module) {
            if (empty($module['sub_modules']) || !isset($module['sub_modules'])) {
                MembershipPurchaseProcessDetail::create([
                    'id' => Str::uuid(),
                    'purchase_process_id' => $process->id,
                    'module_id' => $module['id'],
                    'sub_module_id' => null
                ]);
            } else {
                foreach ($module['sub_modules'] as $subModule) {
                    MembershipPurchaseProcessDetail::create([
                        'id' => Str::uuid(),
                        'purchase_process_id' => $process->id,
                        'module_id' => $module['id'],
                        'sub_module_id' => $subModule['id']
                    ]);
                }
            }
        }
        return MembershipPurchaseProcessResource::make(MembershipPurchaseProcess::with('purchaseProcessDetails')->find($process->id))
        ->additional([
            'modules' => $allModulesUtils
        ]);
    }

    /**
     * Validate Module Membership Purchase Process exists
     *
     * @param string $data
     * @param int $moduleId
     * @param int $subModuleId
     * @return boolean
     */
    private function detailExists($processId, $moduleId, $subModuleId = null)
    {
        return MembershipPurchaseProcessDetail::where('purchase_process_id', $processId)
            ->where('module_id', $moduleId)
            ->where('sub_module_id', $subModuleId)
            ->exists();
    }

    /**
     * Get Membership Purchase Process
     *
     * @param string $companyId
     * @return array
     */
    public function getMembershipPurchaseProcess(string $companyId)
    {
        $allModulesUtils = MembershipHelper::getAllMembershipModules()['modules'];
        $purchaseDetails = MembershipPurchaseProcess::with('purchaseProcessDetails')
        ->where('company_id', $companyId)
        ->where('is_payment', false)->first();
        if($purchaseDetails){
            return MembershipPurchaseProcessResource::make($purchaseDetails)
            ->additional([
                'modules' => $allModulesUtils
            ]);
        }
        return [];
    }

    /**
     * Delete Membership Purchase Process Detail
     *
     * @param array $request
     * @param string $companyId
     * @return void
     */
    public function deleteDetailByIdAndCompany(array $request, string $companyId)
    {
        $detail = MembershipPurchaseProcessDetail::find($request["details_id"])
        ->whereHas('purchaseProcess', function ($query) use($companyId) {
            $query->where('company_id', $companyId)->where('is_payment', false);
        })->first();
        if($detail) {
            $detail->delete();
        }
    }

    /**
     * Update membership purchase process after payment
     *
     * @param array $request
     * @param string $companyId
     * @return void
     */
    public function updatePurchaseProcessPayment(string $membershipId, string $companyId)
    {
        try {
            MembershipPurchaseProcess::where('company_id', $companyId)
                ->where('is_payment', false)
                ->update([
                    'reference_id' => $membershipId,
                    'is_payment' => true,
                ]);
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }
}
