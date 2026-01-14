<?php

namespace App\Infrastructure\Persistence;

use App\Enums\CompanyInformation as EnumsCompanyInformation;
use App\Infrastructure\Gateway\MembershipPayment;
use App\Models\CompanyInformation;
use Carbon\Carbon;

class MembershipEloquent
{
    private $membershipPayment;
    private $companyInformationModel;

    public function __construct(MembershipPayment $membershipPayment, CompanyInformation $companyInformationModel)
    {
        $this->membershipPayment = $membershipPayment;
        $this->companyInformationModel = $companyInformationModel;
    }

    public function getCreditCardTokenId(array $data, string $company_id){
        $cvv = $data['creditCardToken']['securityCode'];
        unset($data['creditCardToken']['securityCode']);
        $responsePayu = $this->membershipPayment->getCreditCardTokenId($data);
        $companyInformation = $this->companyInformationModel::updateOrCreate(
            ['company_id' => $company_id],
            ['payment_information' => json_encode(EnumsCompanyInformation::PAYMENT_INFORMATION)]
        );

        $companyInfo = json_decode($companyInformation->payment_information);
        $companyInfo->transaction->creditCardTokenId = $responsePayu['creditCardToken']['creditCardTokenId'];
        $companyInfo->transaction->paymentMethod = $data['creditCardToken']['paymentMethod'];
        $companyInfo->transaction->creditCard->securityCode = $cvv;

        $companyInformation->payment_information = json_encode($companyInfo);
        $companyInformation->save();

        return $companyInformation;
    }

    public function recurringPaymentRegistration(array $data, string $companyId)
    {
        $dataToken = [
            "language" => 'es',
            "command" => "CREATE_TOKEN",
            "creditCardToken" => $data['transaction']['creditCard']
        ];
        $response = $this->getCreditCardTokenId($dataToken, $companyId);
        $creditCardTokenId = json_decode($response->payment_information)->transaction->creditCardTokenId;

        $data['transaction']['creditCardTokenId'] = $creditCardTokenId;
        $data['transaction']['creditCard'] = [
            'processWithoutCvv2' => true,
            'securityCode' => '000'
        ];

        $companyInformation = $this->companyInformationModel::where('company_id', $companyId)->firstOrFail();
        $companyInformation->payment_information = json_encode($data);
        $companyInformation->save();

        $responsePayu = $this->membershipPayment->cashTransfer($data);

        return $responsePayu;
    }

    /**
     * Delete credit card in PayU and company_information table
     * @param string $companyId uuid
     * @param array $data
     * @return array
     */
    public function deleteCardToken(string $companyId, array $data = []): array
    {
        $companyInformation = $this->companyInformationModel::where('company_id', $companyId)->firstOrFail();
        $companyInfo = json_decode($companyInformation->payment_information);
        $token = $companyInfo->transaction->creditCardTokenId;

        $dataGetToken['creditCardTokenInformation'] = [
            'creditCardTokenId' => $token,
        ];
        $responsePayu = $this->membershipPayment->getCreditCardToken($dataGetToken);

        if (!isset($responsePayu['creditCardTokenList']))
            return [];

        $payerId = collect($responsePayu['creditCardTokenList'])->where('creditCardTokenId', $token)->first()['payerId'];

        $data['removeCreditCardToken'] = [
            'payerId' => $payerId,
            'creditCardTokenId' => $token
        ];

        $responsePayu = $this->membershipPayment->deleteCardToken($data);

        $companyInformation->payment_information = json_encode(EnumsCompanyInformation::PAYMENT_INFORMATION);
        $companyInformation->save();

        return $responsePayu;
    }

    public function paymentWithToken(array $data, string $companyId)
    {
        $companyInformation = $this->companyInformationModel::where('company_id', $companyId)->firstOrFail();
        $companyInfo = json_decode($companyInformation->payment_information, true);
        $companyInfo['transaction']['order']['additionalValues'] = $data;
        $responsePayu = $this->membershipPayment->paymentWithToken($companyInfo);

        $companyInformation->payment_information = json_encode($companyInfo);
        $companyInformation->save();

        return $responsePayu;
    }

    /**
     * Get data card saved into PayU with the creditCardTokenId
     * @param string $companyId uuid
     * @return array
     */
    public function getCardPayu(string $companyId): array
    {
        $companyInformation = $this->companyInformationModel::where('company_id', $companyId)->firstOrFail();
        $companyInfo = json_decode($companyInformation->payment_information);
        $token = $companyInfo->transaction->creditCardTokenId;

        $dataGetToken['creditCardTokenInformation'] = [
            'creditCardTokenId' => $token,
        ];
        $responsePayu = $this->membershipPayment->getCreditCardToken($dataGetToken);

        if (!isset($responsePayu['creditCardTokenList']))
            return [];

        return collect($responsePayu['creditCardTokenList'])->where('creditCardTokenId', $token)->first() ?? [];
    }

    public function getDataPayu(string $companyId): array
    {
        $companyInformation = $this->companyInformationModel::where('company_id', $companyId)->firstOrFail();
        return ['payu_data' => ['transaction' => json_decode($companyInformation->payment_information, true)['transaction']]];
    }
}
