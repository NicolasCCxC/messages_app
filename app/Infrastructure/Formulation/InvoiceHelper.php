<?php

namespace App\Infrastructure\Formulation;


class InvoiceHelper
{
    public static function createClientInvoice(array $data, string $companyId, string $userId)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/invoices/customers',
            'method' => 'POST',
            'service' => 'INVOICE',
            'data' => $data,
            'user_id' => $userId,
            'company_id' => $companyId,
        ]);
    }


    /**
     * method used to invoice electronically
     *
     * @param array $data shipping data for billing
     * @param array $companyId Billing company UUID
     * @param array $userId Billing user UUID
     * @throws GuzzleException
     */
    public static function createMembershipInvoice(array $data, string $companyId, string $userId)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/invoices/bills/electronic/document',
            'method' => 'POST',
            'service' => 'INVOICE',
            'data' => $data,
            'user_id' => $userId,
            'company_id' => $companyId,
        ]);
    }
}
