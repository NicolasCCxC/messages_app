<?php

namespace App\Infrastructure\Gateway;

use App\Infrastructure\Persistence\CompanyPaymentGatewayEloquent;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class HandlePayment
{

    public $companyPaymentGatewayEloquent;

    public function __construct(CompanyPaymentGatewayEloquent $companyPaymentGatewayEloquent)
    {
        $this->companyPaymentGatewayEloquent = $companyPaymentGatewayEloquent;
    }

    public function methods(string $id, string $companyId)
    {

        $credentials = $this->companyPaymentGatewayEloquent->decryptCredentials($id, $companyId);

        $gateway = GatewayFactory::createAGateway($id, $credentials);

        if(!$gateway)
        {
            throw new BadRequestException();
        }

        return $gateway->allowPaymentMethods();
    }

    public function pse(string $id, string $companyId)
    {
        $credentials = $this->companyPaymentGatewayEloquent->decryptCredentials($id, $companyId);
        $gateway = GatewayFactory::createAGateway($id, $credentials);

        if(!$gateway)
        {
            throw new BadRequestException();
        }

        return $gateway->getPseBanks();
    }

    public function pseTransfer(string $id, array $data, string $companyId, string $clientId)
    {
        $paymentConfig = $this->companyPaymentGatewayEloquent->decryptCredentials($id, $companyId);

        $gateway = GatewayFactory::createAGateway($id, $paymentConfig);

        if(!$gateway)
        {
            throw new BadRequestException();
        }

        $data['client_id'] = $clientId;
        $data['company_information_id'] = $paymentConfig['model']->company_information_id;
        $data['company_payment_gateways'] = $paymentConfig['model']->company_payment_gateways;
        return $gateway->pseTransfer($data, $companyId, $clientId);
    }

    public function creditCardTransfer(string $id, array $data, string $companyId, string $clientId)
    {
        $paymentConfig = $this->companyPaymentGatewayEloquent->decryptCredentials($id, $companyId);

        $gateway = GatewayFactory::createAGateway($id, $paymentConfig);

        if(!$gateway)
        {
            throw new BadRequestException();
        }

        $data['client_id'] = $clientId;
        $data['company_information_id'] = $paymentConfig['model']->company_information_id;
        $data['company_payment_gateways'] = $paymentConfig['model']->company_payment_gateways;
        return $gateway->creditCardTransfer($data, $companyId, $clientId);
    }

    public function report(string $id, string  $transactionId, string $companyId, string $clientId)
    {
        $paymentConfig = $this->companyPaymentGatewayEloquent->decryptCredentials($id, $companyId);

        $gateway = GatewayFactory::createAGateway($id, $paymentConfig);

        if(!$gateway)
        {
            throw new BadRequestException();
        }

        return $gateway->report($transactionId, $companyId, $clientId);
    }

    public function cashTransfer(string $id, array $data, string $companyId, string $clientId)
    {
        $paymentConfig = $this->companyPaymentGatewayEloquent->decryptCredentials($id, $companyId);

        $gateway = GatewayFactory::createAGateway($id, $paymentConfig);

        if(!$gateway)
        {
            throw new BadRequestException();
        }

        $data['client_id'] = $clientId;
        $data['company_information_id'] = $paymentConfig['model']->company_information_id;
        $data['company_payment_gateways'] = $paymentConfig['model']->company_payment_gateways;
        return $gateway->cashTransfer($data,$companyId,$clientId);
    }

}
