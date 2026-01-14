<?php

namespace App\Infrastructure\Persistence;

use App\Models\CompanyPaymentGateway;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;

class CompanyPaymentGatewayEloquent
{
    private $companyPaymentGatewayModel;
    private $companyInformationEloquent;

    public function __construct(
        CompanyPaymentGateway $companyPaymentGateway,
        CompanyInformationEloquent $companyInformationEloquent)
    {
        $this->companyPaymentGatewayModel = $companyPaymentGateway;
        $this->companyInformationEloquent = $companyInformationEloquent;
    }

    public function store($data, $companyId)
    {
        $data['company_information_id'] = $this->companyInformationEloquent->store(['company_id' => $companyId]);

        $data['credentials'] = $this->encryptKeys($data['credentials']);
        $this->companyPaymentGatewayModel::updateOrCreate(
            [
                'payment_gateway_id' => $data['payment_gateway_id'],
                'company_information_id' => $data['company_information_id']->id
            ],
            [
                'date' => $data['date'],
                'credentials' => $data['credentials']
            ]);

        return $this->getAll($companyId);
    }

    private function encryptKeys(array $data): array
    {
        return collect($data)->map(function ($item) {
            return  Crypt::encrypt($item);
        })
            ->toArray();
    }

    public function decryptCredentials (string $idGateway, string $companyId)
    {
        $information = $this->companyPaymentGatewayModel::whereHas('companyInformation' ,
            function (Builder $builder) use ($companyId) {
                $builder->where('company_id', $companyId);
            })
            ->where('payment_gateway_id', $idGateway)
            ->firstOrFail();

        $decryptCredentials = collect($information->credentials)
            ->map(function ($item) {
                return Crypt::decrypt($item);
            });

        return [
            'credentials' => $decryptCredentials->toArray(),
            'model' => $information
        ];
    }

    public function get(string $id)
    {
        return $this->companyPaymentGatewayModel::findOrFail($id);
    }

    public function getAll(string $companyId)
    {
        return $this->companyPaymentGatewayModel::with(['paymentGateway'])
            ->whereHas('companyInformation', function (Builder $query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->get();
    }

}
