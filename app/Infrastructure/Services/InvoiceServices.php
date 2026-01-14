<?php

namespace App\Infrastructure\Services;

use App\Enums\Services;
use App\Traits\CommunicationBetweenServicesTrait;

class InvoiceServices
{
    use CommunicationBetweenServicesTrait;

    public function createInvoice(array $data, string $companyId, string $userId)
    {
        return collect(
            $this->makeRequest(
                'POST',
                Services::INVOICE,
                '/invoices/bills/make-bill-from-purchase-order',
                $userId,
                $companyId,
                $data
            )
        );
    }

    public function updateStatusPurchase(array $data, string $companyId, string $userId)
    {
        return collect(
            $this->makeRequest(
                'PUT',
                Services::INVOICE,
                '/invoices/bills/purchase/'.$data['purchase_order_id'],
                $userId,
                $companyId,
                [
                   'payment_status' => $data['status']
                ]
            )
        );
    }

    /**
     * Get Purchase Order Information
     *
     * @param string $purchaseOrderId
     * @param string $userId
     * @param string $companyId
     *
     * @services INVOICE /purchase-order
     *
     * @return mixed
     */
    public function getPurchaseOrderInformation(string $purchaseOrderId, string $companyId, string $userId)
    {
        return collect(
            $this->makeRequest(
                'GET',
                Services::INVOICE,
                '/invoices/bills/purchase-order/get-information/'.$purchaseOrderId,
                $userId,
                $companyId
            )
        );
    }
}
