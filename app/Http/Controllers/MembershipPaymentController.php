<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Requests\Membership\DetailTransactionPayuRequest;
use App\Http\Requests\Membership\GetCreditCardTokenRequest;
use App\Http\Requests\Membership\PayWithOutTokenRequest;
use App\Http\Requests\Membership\PayWithTokenRequest;
use App\Http\Requests\Membership\RecurringPaymentRegistrationRequest;
use App\Infrastructure\Gateway\MembershipPayment;
use App\Infrastructure\Persistence\MembershipEloquent;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MembershipPaymentController extends Controller
{

    private $membershipPayment;
    private $membershipEloquent;

    public function __construct(MembershipPayment $membershipPayment, MembershipEloquent $membershipEloquent)
    {
        $this->membershipPayment = $membershipPayment;
        $this->membershipEloquent = $membershipEloquent;
    }

    public function pse(Request $request)
    {
        return $this->successResponse(
            $this->membershipPayment->pse($request->all())
        );
    }

    public function cash(Request $request)
    {
        return $this->successResponse(
            $this->membershipPayment->cash($request->all())
        );
    }

    public function paymentReport(string $transactionId)
    {
        return $this->successResponse(
            $this->membershipPayment->paymentReport($transactionId)
        );
    }

    public function getPseBanks()
    {
        return $this->successResponse(
            $this->membershipPayment->pseBanks()
        );
    }

    public function getCreditCardTokenId(GetCreditCardTokenRequest $request)
    {
        $data = $request->all();
        $companyId = $request->header('company-id');
        $isValidCreditCard = Utils::luhn_check($data['creditCardToken']['number']);
        $paymentMethod = Utils::checkCreditCardType($data['creditCardToken']['number'], $data['creditCardToken']['paymentMethod']);
        if (!($isValidCreditCard && $paymentMethod))
            return $this->errorResponse(Response::HTTP_BAD_REQUEST, 'Invalid credit card',);

        return $this->successResponse(
            $this->membershipEloquent->getCreditCardTokenId($data, $companyId)
        );
    }

    public function recurringPaymentRegistration(RecurringPaymentRegistrationRequest $request)
    {
        $data = $request->all();
        $companyId = $request->header('company-id');

        $isValidCreditCard = Utils::luhn_check($data['transaction']['creditCard']['number']);
        $paymentMethod = Utils::checkCreditCardType($data['transaction']['creditCard']['number'], $data['transaction']['creditCard']['paymentMethod']);
        if (!($isValidCreditCard && $paymentMethod))
            return $this->errorResponse(Response::HTTP_BAD_REQUEST, 'Invalid credit card',);

        return $this->successResponse(
            $this->membershipEloquent->recurringPaymentRegistration($data, $companyId)
        );
    }

    /**
     * Delete credit card
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteCardToken(Request $request): JsonResponse
    {
        $companyId = $request->header('company-id');
        return $this->successResponse(
            $this->membershipEloquent->deleteCardToken($companyId)
        );
    }

    public function paymentWithToken(PayWithTokenRequest $request){
        $companyId = $request->header('company-id');
        $data = $request->all();
        return $this->successResponse(
            $this->membershipEloquent->paymentWithToken($data, $companyId)
        );
    }

    public function paymentWithOutToken(PayWithOutTokenRequest $request){
        $companyId = $request->header('company-id');
        $data = $request->all();

        $isValidCreditCard = Utils::luhn_check($data['transaction']['creditCard']['number']);
        $paymentMethod = Utils::checkCreditCardType($data['transaction']['creditCard']['number'], $data['transaction']['paymentMethod']);
        if (!($isValidCreditCard && $paymentMethod))
            return $this->errorResponse(Response::HTTP_BAD_REQUEST, 'Invalid credit card',);

        return $this->successResponse(
            $this->membershipPayment->paymentWithOutToken($data, $companyId)
        );
    }

    /**
     * Get card payu
     * @param Request $request
     * @return JsonResponse
     */
    public function getCardPayu(Request $request){
        $companyId = $request->header('company-id');

        return $this->successResponse(
            $this->membershipEloquent->getCardPayu($companyId)
        );
    }

    public function getDetailsTransaction(DetailTransactionPayuRequest $request){
        $data = $request->all();
        return $this->successResponse(
            $this->membershipPayment->getDetailsTransaction($data)
        );
    }

    public function getDataPayu(string $companyId):  JsonResponse{
        return $this->successResponse(
            $this->membershipEloquent->getDataPayu($companyId)
        );
    }
}
