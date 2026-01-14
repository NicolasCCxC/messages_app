<?php

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Formulation\ShoppingHelper;
use App\Infrastructure\Formulation\WebsiteHelper;
use App\Models\Company;
use App\Models\PhysicalStore;

class InitialDataEloquent
{

    private $response;

    public function __construct()
    {
        $this->response = [
            'website' => [],
            'shopping-cart' => []
        ];
    }

    public function setUp(array $data)
    {
        $this->response['website'] = $this->getWebsite($data['domain']);
        $companyId = $this->response['website']['company_id'];

        if(auth()->guard('client-api')){
            $this->response['shopping-cart'] = $this->getShoppingCart($companyId);
        }

        $companyModel = new Company();
        $physicalStoreModel = new PhysicalStore();
        $this->response['is_billing_us'] = $companyModel::findOrFail($companyId)->is_billing_us;
        $this->response['physical_store'] = $physicalStoreModel::where('company_id', $companyId)->get();
        
        return $this->response;
    }

    private function getWebsite(string $domain)
    {
        return WebsiteHelper::getWebsite([
            "domain" => $domain,
        ]);
    }

    private function getShoppingCart($companyId)
    {
        return ShoppingHelper::getShopCart($companyId);
    }

}
