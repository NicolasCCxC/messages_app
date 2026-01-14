<?php

namespace App\Infrastructure\Persistence;

use App\Enums\CompanyInformation as EnumsCompanyInformation;
use App\Models\CompanyInformation;
use Faker\Provider\Uuid;

class CompanyInformationEloquent
{

    private $companyInformationModel;

    public function __construct(CompanyInformation $companyInformation)
    {
        $this->companyInformationModel = $companyInformation;
    }

    public function store(array $data)
    {
        return $this->companyInformationModel::updateOrCreate(
                ['company_id' => $data['company_id']],
                ['payment_information' => json_encode(EnumsCompanyInformation::PAYMENT_INFORMATION)]
        );
    }

    public function getByCompany(string $companyId)
    {
        $information = $this->companyInformationModel::where('company_id', $companyId)->first();

        if(isset($information)) return $information;

        return $this->store(['company_id' => $companyId]);
    }

}
