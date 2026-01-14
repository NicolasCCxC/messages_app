<?php

namespace App\Infrastructure\Services;

use App\Infrastructure\Formulation\GatewayHelper;
use Illuminate\Support\Str;

class InvoiceService
{
    public function getNumberInvoicesCreated($companyId = null)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/invoices/quantityInvoices',
            'method' => 'POST',
            'service' => 'INVOICE',
            'data' => [],
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? ($companyId ?? Str::uuid()->toString()),
        ]);
    }

    public function getCheckOccurrence(array $data)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/invoices/check-occurrence/invoice-foreign-exchange',
            'method' => 'POST',
            'service' => 'INVOICE',
            'data' => $data,
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
        ]);
    }

    public function getLastConsecutiveByPrefix(string $companyId, array $data = [])
    {
        return GatewayHelper::routeHandler([
            'resource' => '/invoices/consecutives/last-by-prefix',
            'method' => 'POST',
            'data' => $data,
            'service' => 'INVOICE',
            'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
            'company_id' => $companyId,
        ]);
    }

    public function storeCustomer(array $request, string $companyId)
    {
        return GatewayHelper::routeHandler([
            'resource' => '/invoices/customers/client',
            'method' => 'POST',
            'data' => $request,
            'service' => 'INVOICE',
            'user_id' => $request['client_id'] ?? Str::uuid()->toString(),
            'company_id' => $companyId,
        ]);
    }
}
