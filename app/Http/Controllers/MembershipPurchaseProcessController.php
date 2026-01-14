<?php

namespace App\Http\Controllers;

use App\Models\MembershipPurchaseProcess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ResponseApiTrait;
use App\Http\Requests\Membership\StorePurchaseProcessRequest;
use App\Infrastructure\Persistence\MembershipPurchaseProcessEloquent;
use App\Helpers\ExtractJwtJsonHelper;
use App\Models\Module;

class MembershipPurchaseProcessController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var MembershipEloquent
     */
    private $purchaseProcessEloquent;

    public function __construct(MembershipPurchaseProcessEloquent $purchaseProcessEloquent)
    {
        $this->purchaseProcessEloquent = $purchaseProcessEloquent;
    }
    /**
     * Store a newly created membership purchase process.
     *
     * @param StorePurchaseProcessRequest $request
     * @return JsonResponse
     */
    public function store(StorePurchaseProcessRequest $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->purchaseProcessEloquent->storeMembershipPurchaseProcess($request->all(),$payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * Get membership purchase process.
     *
     * @param Request $membershipPurchaseProcess
     * @return JsonResponse
     */
    public function getMembershipPurchaseProcess(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->purchaseProcessEloquent->getMembershipPurchaseProcess($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * Delete membership purchase process.
     *
     * @param Request $membershipPurchaseProcess
     * @return JsonResponse
     */
    public function deleteDetailByIdAndCompany(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->purchaseProcessEloquent->deleteDetailByIdAndCompany($request->all(),$payload["company_id"]),
            Module::SECURITY
        );
    }
}
