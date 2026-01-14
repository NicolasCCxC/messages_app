<?php

namespace App\Infrastructure\Persistence;

use App\Models\SecurityFiscalResponsibility;

class FiscalResponsibilityEloquent
{

    private $model;

    public function __construct(SecurityFiscalResponsibility $model)
    {
        $this->model = $model;
    }

    public function storeMany(array $data, string $companyId)
    {
        $this->model::where('company_id',$companyId)->delete();
        collect($data)->each(function ($item) use ($companyId) {
            $this->model::create([
                'company_id' => $companyId,
                'code_fiscal_responsibility' => $item['id'],
                'number_resolution' => $item['number_resolution'] ?? null,
                'date' => $item['date'] ?? null,
                'withholdings' => $item['withholdings'] ?? []
            ]);
        });
    }

}
