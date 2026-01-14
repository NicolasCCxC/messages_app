<?php

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Formulation\UtilsHelper;
use App\Infrastructure\Services\InvoiceService;
use App\Models\CompanyForeignExchange;
use Illuminate\Support\Str;

class CompanyForeignExchangeEloquent
{

    private $model;
    private $invoiceService;

    public function __construct()
    {
        $this->model = new CompanyForeignExchange();
        $this->invoiceService = new InvoiceService();
    }

    public function store(array $request)
    {
        $foreignExchange = $this->model::where('foreign_exchange_id', $request['foreign_exchange_id'])->where('company_id', $request['company_id'])->first();
        if (!isset($request['id']) && isset($foreignExchange)) {
            $request['id'] = $foreignExchange->id;
        }
        return $this->model::updateOrCreate(
            ['id' => $request['id'] ?? Str::uuid()->toString()],
            $request
        );
    }

    public function getAll(array $request, string $companyId)
    {
        $dynamicResource = [
            [
                'model' => 'ForeignExchange',
                'constraints' => [],
                'fields' => [],
                'multiple_record' => true
            ]
        ];
        $utils = UtilsHelper::dynamicResource($dynamicResource)['foreign_exchange'];
        $isActive = !array_key_exists('is_active', $request) ? [true, false] : $request['is_active'];
        $dataForeignExchange = $this->model::where('company_id', $companyId)->whereIn('is_active', $isActive)->get();
        return $dataForeignExchange->map(function ($foreignExchanges) use ($utils){
            $foreignExchange = collect($utils)->where('id', $foreignExchanges['foreign_exchange_id'])->first();
            $foreignExchange['is_active'] = $foreignExchanges['is_active'];
            return $foreignExchange;
        });
    }

    public function update(array $request, string $id)
    {
        $this->model::findOrFail($id)->update($request);
        return $this->model::findOrFail($id);
    }

    public function delete(array $request, string $companyId)
    {
        $array = $this->model::whereIn('id', $request)->pluck('foreign_exchange_id')->unique()->flatten()->toArray();
        $invoices = $this->invoiceService->getCheckOccurrence($array);
        foreach ($invoices['data'] as $value) {
            $foreignExchange = $this->model::where('foreign_exchange_id', $value)->where('company_id', $companyId)->first();
            if($foreignExchange){
                $this->model::findOrFail($foreignExchange['id'])->delete();
            }
        }
        return $this->model::where('company_id', $companyId)->get();
    }

    public static function getCompanyForeignExchange(string $companyId)
    {
        $dynamicResource = [
            [
                'model' => 'ForeignExchange',
                'constraints' => [],
                'fields' => [],
                'multiple_record' => true
            ]
        ];
        $utils = UtilsHelper::dynamicResource($dynamicResource)['foreign_exchange'];
        $model = new CompanyForeignExchange();
        $dataForeignExchange = $model::where('company_id', $companyId)->where('is_active', true)->get();
        return $dataForeignExchange->map(function ($foreignExchanges) use ($utils){
            $foreignExchange = collect($utils)->where('id', $foreignExchanges['foreign_exchange_id'])->first();
            $foreignExchange['companies_foreign_exchange_id'] = $foreignExchanges['id'];
            $foreignExchange['is_active'] = $foreignExchanges['is_active'];
            return $foreignExchange;
        });
    }
}
