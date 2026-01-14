<?php


namespace App\Infrastructure\Formulation;


use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use App\Models\Company;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompaniesAdministrationResource;
use App\Helpers\CompaniesAdministrationHelper;

class CompanyHelper
{
    /**
     *
     * @return Collection
     * @throws GuzzleException
     */
    public static function getCompanyInfo(string $companyId = null)
    {

        $companyModel = new Company();

        if (!$companyId) return [];

        $company = $companyModel::findOrFail($companyId);

        $dynamicResource = [
            [
                'model' => 'TaxDetail',
                'constraints' => [
                    [
                        'field' => 'id',
                        'operator' => '=',
                        'parameter' => $company->tax_detail,
                    ]
                ],
                'fields' => [
                ],
                'multiple_record' => false
            ],
            [
                'model' => 'FiscalResponsibility',
                'constraints' => [
                ],
                'fields' => [
                ],
                'multiple_record' => true
            ],
            [
                'model' => 'ForeignExchange',
                'constraints' => [],
                'fields' => [],
                'multiple_record' => true
            ],
            [
                'model' => 'Ciiu',
                'constraints' => [
                    [
                        'field' => 'code',
                        'operator' => '=',
                        'parameter' => $company->ciius()->where('is_main', true)->get()->first()->code ?? 0
                    ]
                ],
                'fields' => [
                ],
                'multiple_record' => false
            ]
        ];

        $modules = MembershipHelper::getAllMembershipModules()['modules'];
        $utils = UtilsHelper::dynamicResource($dynamicResource);

        return CompanyResource::make($company)->additional([
            'modules' => $modules,
            'utils' => $utils
        ]);
    }

    /**
     *
     * @return Collection
     * @throws GuzzleException
    */
    public static function getInformationCompanies($administrationData = [])
    {
        $dynamicResource = ['type_tax_payer','document_types','membership_sub_modules'];
        $modules = MembershipHelper::getAllMembershipModules()['modules'];
        $utils = UtilsHelper::getUtils($dynamicResource);
        $companyInformation = [];
        collect($administrationData)->map(function ($company) use($modules, $utils, &$companyInformation) {
                $companyInformation[] =  CompaniesAdministrationHelper::companiesAdministrationDataReform($company, $modules, $utils);
        });
        return Collect($companyInformation);
    }
}
