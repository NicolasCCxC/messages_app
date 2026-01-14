<?php

namespace App\Http\Controllers;

use App\Http\Requests\Membership\CancelMembershipRequest;
use App\Http\Requests\Membership\InvoiceMembershipRequest;
use App\Http\Requests\Membership\StoreMembershipRequest;
use App\Http\Requests\Membership\ValidateAccessFreeDocumentsRequest;
use App\Http\Requests\PayMembershipRequest;
use App\Infrastructure\Persistence\MembershipEloquent;
use App\Infrastructure\Persistence\PayTransactionEloquent;
use App\Helpers\ExtractJwtJsonHelper;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MembershipController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var MembershipEloquent
     */
    private $membershipEloquent;

    /**
     * @var PayTransactionEloquent
     */
    private $payTransactionEloquent;

    /**
     * @var Request
     */
    private $request;


    public function __construct(MembershipEloquent $membershipEloquent, Request $request)
    {
        $this->membershipEloquent = $membershipEloquent;
        $this->request = $request;
        $this->payTransactionEloquent = new PayTransactionEloquent();
    }

    /**
     * @param Request $request - request information necesary
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function index(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->membershipEloquent->getMembershipStatus($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * @param StoreMembershipRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function storePayAndCreateToken(StoreMembershipRequest $request): JsonResponse
    {
        $request['option_pay'] = 'PAY_AND_CREATE_TOKEN';
        return $this->successResponse(
            $this->membershipEloquent->storeMembership($request->all()),
            Module::SECURITY
        );
    }

    public function storePayWithToken(StoreMembershipRequest $request): JsonResponse
    {
        $request['option_pay'] = 'PAY_WITH_TOKEN';
        return $this->successResponse(
            $this->membershipEloquent->storeMembership($request->all()),
            Module::SECURITY
        );
    }

    public function storePayWithoutToken(StoreMembershipRequest $request): JsonResponse
    {
        $request['option_pay'] = 'PAY_WITHOUT_TOKEN';
        return $this->successResponse(
            $this->membershipEloquent->storeMembership($request->all()),
            Module::SECURITY
        );
    }

    public function storePayPse(StoreMembershipRequest $request): JsonResponse
    {
        $request['option_pay'] = 'PAY_PSE';
        return $this->successResponse(
            $this->membershipEloquent->storeMembership($request->all()),
            Module::SECURITY
        );
    }

    public function pay(PayMembershipRequest $request)
    {
        return $this->successResponse(
            $this->membershipEloquent->payMembership($request->all()),
            Module::SECURITY
        );
    }

    public function updateStatusPay(string $transactionId)
    {
        return $this->successResponse(
            $this->payTransactionEloquent->validatePendingTransaction($transactionId),
            Module::SECURITY
        );
    }

    /**
     * @param CancelMembershipRequest $request
     * @return JsonResponse
     */
    public function cancelModulesMemberships(CancelMembershipRequest $request): JsonResponse
    {
        $credentials = [
            "email" => auth()->user()->email,
            "password" => $request->password
        ];

        if (!auth()->attempt($credentials)) {
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_UNAUTHORIZED,
                'Unauthorized'
            );
        }

        return $this->successResponse(
            $this->membershipEloquent->cancelModulesMemberships($request->all()),
            Module::SECURITY
        );
    }

    public function validateModules(StoreMembershipRequest $request)
    {
        return $this->successResponse(
            $this->membershipEloquent->toValidateIfChargeNewMembership($request->all()),
            Module::SECURITY
        );
    }

    /**
     * Get the number of pages available for a company
     *
     * @param Request $request - request information necesary
     *
     * @return JsonResponse
     */
    public function getPagesAvailable(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->membershipEloquent->getPagesAvailable($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * Validate the status of transactions for a company
     *
     * @return JsonResponse
     */
    public function validateStatusTransaction(): JsonResponse
    {
        $company_id = $this->request->header('company_id');
        return $this->successResponse(
            $this->payTransactionEloquent->validateStatusTransaction($company_id),
            Module::SECURITY
        );
    }

    /**
     * Return filtered memberships by company_id
     *
     * @param Request $request - request information necesary
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getDetailsMembership(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->membershipEloquent->getDetailsMembership($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * Create a binnacle memberships by company_id
     *
     * @param Request $request - request information necesary
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getBinnacleMembership(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->membershipEloquent->getBinnacleMembership($payload["company_id"]),
            Module::SECURITY
        );
    }
    
    /**
     * Create a free documents Membership
     *
     * @param Request $request - request information necesary
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function validateAccessFreeDocuments(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->membershipEloquent->validateAccessFreeDocuments($payload["company_id"],$request->all()),
            Module::SECURITY
        );
    }
}
