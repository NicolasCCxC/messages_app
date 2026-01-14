<?php


namespace App\Infrastructure\Persistence;


use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyAccountCreated;
use App\Http\Requests\Company\UpdateCompanyMinimunDataRequest;
use App\Http\Resources\Company\CompanyResource as CompanyCompanyResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyWithEmailResource;
use App\Http\Resources\MembershipCollectionResource;

use App\Infrastructure\Formulation\BinnacleHelper;
use App\Infrastructure\Formulation\BucketHelper;
use App\Infrastructure\Formulation\InventoryHelper;
use App\Infrastructure\Formulation\UtilsHelper;
use App\Infrastructure\Formulation\WebsiteHelper;
use App\Infrastructure\Formulation\CompanyHelper;
use App\Models\Company;
use App\Models\Role;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Services\InvoiceService;
use App\Models\Membership;
use App\Infrastructure\Services\BinnacleService;
use App\Infrastructure\Services\WebsiteService;
use App\Models\Client;
use Carbon\Carbon;

class CompanyEloquent
{
    private $companyModel;
    private $clientModel;

    /**
     * @var UserEloquent
     */
    private $userEloquent;

    /**
     * @var MembershipEloquent
     */
    private $membershipsEloquent;

    /**
     * @var ClientEloquent
     */
    private $clientEloquent;

    /**
     * @var CiiuEloquent
     */
    private $ciiuEloquent;

    /**
     * @var AttachmentEloquent
     */
    private $attachmentEloquent;

    /**
     * @var FiscalResponsibilityEloquent
     */
    private $fiscalResponsibility;

    /**
     * @var CompanyForeignExchangeEloquent
     */
    private $CompanyForeignExchangeEloquent;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var WebsiteService
     */
    private $websiteService;

    /**
     * Type attachment: Logo
     */
    public const LOGO = 'logo';

    public function __construct(
        UserEloquent       $userEloquent,
        MembershipEloquent $membershipsEloquent,
        ClientEloquent     $clientEloquent,
        CiiuEloquent       $ciiuEloquent,
        AttachmentEloquent $attachmentEloquent,
        FiscalResponsibilityEloquent $fiscalResponsibility,
        CompanyForeignExchangeEloquent $CompanyForeignExchangeEloquent
    ) {
        $this->userEloquent = $userEloquent;
        $this->membershipsEloquent = $membershipsEloquent;
        $this->clientEloquent = $clientEloquent;
        $this->companyModel = new Company();
        $this->ciiuEloquent = $ciiuEloquent;
        $this->attachmentEloquent = $attachmentEloquent;
        $this->fiscalResponsibility = $fiscalResponsibility;
        $this->invoiceService = new InvoiceService();
        $this->websiteService = new WebsiteService();
        $this->CompanyForeignExchangeEloquent = $CompanyForeignExchangeEloquent;
        $this->clientModel = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function getCompanyInfo(string $companyId = null)
    {
        return CompanyHelper::getCompanyInfo($companyId);
    }

    /**
     * @throws GuzzleException
     */
    public function getCompanyByDocument(array $request)
    {
        return Company::where("document_type", $request["document_type"])->where("document_number", $request["document_number"])->first();
    }

    /**
     * @throws GuzzleException
     */
    public function getCompanyInfoWithEmail(string $companyId = null)
    {
        if (!$companyId) return [];
        $company = $this->companyModel::with(['users'])->findOrFail($companyId)->whereHas('users.role', function ($query) {
            $query->where('name', Role::Main);
        })->get()->first();

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
                'fields' => [],
                'multiple_record' => false
            ],
            [
                'model' => 'DocumentType',
                'constraints' => [
                    [
                        'field' => 'id',
                        'operator' => '=',
                        'parameter' => $company->document_type,
                    ]
                ],
                'fields' => [],
                'multiple_record' => false
            ],
            [
                'model' => 'FiscalResponsibility',
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
                'fields' => [],
                'multiple_record' => false
            ]
        ];
        $utils = UtilsHelper::dynamicResource($dynamicResource);
        return CompanyWithEmailResource::make($company)->additional([
            'utils' => $utils
        ]);
    }

    /**
     * Store new company and the admin user
     * @param array $request
     * @param string $ip
     * @return CompanyResource
     * @throws GuzzleException
     */
    public function storeCompany(array $request, string $ip = null): array
    {
        $company = [];
        if (auth()->user()->company_id) {

            if (isset($request['domain'])) {
                $website = WebsiteHelper::updateDomain([
                    'company_id' => auth()->user()->company_id,
                    'domain' => $request['domain'],
                    'company_name' => auth()->user()->name
                ]);
                $request['domain'] = $website['domain'] ?? $request['domain'];
                $attachment = $this->attachmentEloquent->getAttachment(auth()->user()->company_id, self::LOGO);
                $bucketDetail = BucketHelper::getElectronicInvoicePreview([
                    'domain' => $request['domain'],
                    'logo' => $attachment['bucket_id'] ?? null,
                    'company_name' => $request['name'] ?? null,
                    'supplier_economic_activity' => $request['ciius'] ? implode(',', array_column($request['ciius'], 'code')) : null,
                    'supplier_responsibilities_resolutions' => [
                        'code' => 'R-99-PN'
                    ]
                ]);
                abort_if(
                    !isset($bucketDetail['url']),
                    Response::HTTP_BAD_REQUEST,
                    'There is an error in the generation of the invoice preview document'
                );

                $this->attachmentEloquent->updateAttachment([
                    'file' => $attachment['name'] ?? self::LOGO,
                    'id' => $attachment['bucket_id'] ?? null
                ], auth()->user()->company_id, $bucketDetail['url']);
            }

            $this->companyModel::find(auth()->user()->company_id)
                ->update($request);

            $company = $this->companyModel::find(auth()->user()->company_id);
            BinnacleHelper::internalActivity(
                $ip,
                auth()->user()->id,
                auth()->user()->name,
                auth()->user()->email,
                $company->id,
                'Perfil de la empresa',
                'Modificó información de la empresa'
            );
        } else {

            $company = DB::transaction(function () use ($request) {
                $request['brand_established_service'] = false;
                $company = $this->companyModel->query()->create($request);
                $this->userEloquent->assignCompanyAndSuperAdmin(auth()->user()->id, $company->id);
                return $company;
            });
        }

        if (isset($request['ciius'])) {
            $this->ciiuEloquent->storeCiuu($company['id'], $request['ciius']);
            InventoryHelper::updateCategoriesAndProductTypesDefault($request['ciius'], $company['id']);
        }

        if (array_key_exists('fiscal_responsibilities', $request)) {
            $this->fiscalResponsibility->storeMany($request['fiscal_responsibilities'], $company['id']);
        }

        if (array_key_exists('companies_foreign_exchange', $request)) {
            foreach ($request['companies_foreign_exchange'] as $value) {
                $this->CompanyForeignExchangeEloquent->store([
                    'foreign_exchange_id' => isset($value['foreign_exchange_id']) ? $value['foreign_exchange_id'] : $value['id'],
                    'is_active' => $value['is_active'],
                    'company_id' => $company['id']
                ]);
            }
        }

        return [
            'company' => $this->getCompanyInfo($company['id']),
            'user' => $this->userEloquent->getUserById(auth()->user()->id)
        ];
    }

    public function find($id)
    {
        return Company::query()->findOrFail($id);
    }

    /**
     * @param $id
     * @param UpdateCompanyRequest $request
     * @return CompanyResource
     * @throws GuzzleException
     */
    public function update($id, UpdateCompanyRequest $request): CompanyResource
    {
        $company = $this->companyModel->find($id);
        $company->update($request->validated());
        $company->touch();
        return $this->getCompanyInfo($id);
    }

    public function getUserAndClients(string $companyId)
    {
        return [
            'users' => $this->userEloquent->getAllUsers($companyId),
            'clients' => $this->clientEloquent->getAllCompanyClients($companyId)
        ];
    }

    /**
     * Store an attachment to the company
     *
     * @param array $data
     * @param string $company
     * @param string $name
     * @return array
     * @throws GuzzleException
     */
    public function createUpdateCompanyAttachment(array $data, string $company, string $name, string $ip): array
    {
        $this->find($company);
        $attachment = $this->attachmentEloquent->updateCreate($data, $company, $name, $ip);

        return [
            'bucket_id' => $data['id'] ?? null,
            'information_img' => $data['logo'] ?? null,
            'invoice_pdf_url' => $attachment['preview_url'],
            'supporting_document_pdf_url' => $attachment['supporting_document_preview_url']
        ];
    }

    /**
     * @param string $id
     * @param string $file
     * @return array
     * @throws GuzzleException
     */
    public function deleteCompanyAttachment(string $id, string $file): array
    {
        $dataCompany = $this->companyModel->findOrFail($id);
        $this->attachmentEloquent->deleteAttachment($dataCompany['id'], $file);
        return ['success' => 'Success Operation'];
    }

    /**
     * @param string $companyId
     * @param string $file
     * @param string $ip
     * @return Collection
     * @throws GuzzleException
     */
    public function getCompanyAttachment(string $companyId, string $file, string $ip): Collection
    {
        $attachment = $this->attachmentEloquent->getAttachment($companyId, $file);
        $companyInformation = $this->find($companyId);
        if (is_null($attachment) && in_array($file, ['logo', 'logo-support-documents', 'logo-invoice'])) {
            $companyAttachment =  $this->createUpdateCompanyAttachment([
                'company_name' => $companyInformation['name']
            ], $companyId, $file, $ip);
            $data = array();
            $data['company_name'] = $companyInformation['name'];
            $data['invoice_pdf_url'] = $companyAttachment['invoice_pdf_url'];
            $data['supporting_document_pdf_url'] = $companyAttachment['supporting_document_pdf_url'];
            return collect($data);
        } else if (!is_null($attachment)) {
            if ($attachment['bucket_id'] !== null) {
                $bucketDetails = BucketHelper::getUrl($attachment['bucket_id']);
                $bucketDetails['company_name'] = $companyInformation['name'];
                $bucketDetails['preview_url'] = $attachment['preview_url'];
                $bucketDetails['supporting_document_preview_url'] = $attachment['supporting_document_preview_url'];
                return collect($bucketDetails);
            } else {
                return collect([
                    'company_name' => $companyInformation['name'],
                    'preview_url' => $attachment['preview_url'],
                    'supporting_document_preview_url' => $attachment['supporting_document_preview_url']
                ]);
            }
        } else {
            throw new ModelNotFoundException();
        }
    }

    public function updateDomain(array $data)
    {
        return $this->companyModel->find($data['company_id'])
            ->update($data);
    }

    public function updateUserOrInvoicesAvailable(int $quantityInvoices = 0, int $quantityUsers = 0, string $companyId): array
    {
        $company = $this->companyModel->find($companyId);
        $newQuantityInvoices = $quantityInvoices + $company->invoices_available;
        $newQuantityUsers = $quantityUsers + $company->users_available;
        $company->users_available = $newQuantityUsers;
        $company->save();
        return ['invoices_available' => $newQuantityInvoices, 'users_available' => $company->users_available];
    }


    /**
     * Retrieves the number of invoices available for a specific company.
     *
     * This function calculates the number of invoices available based on memberships and their modules,
     * taking into account approved payments with a specific payment method. It also checks for any
     * unlimited invoice submodules and evaluates the total documents purchased versus those used.
     *
     * @param string $companyId The UUID of the company for which to get the invoice availability.
     * @return array An array containing:
     *               - 'number_invoice': The number of invoices available.
     *               - 'is_unlimited': A boolean indicating if unlimited invoices are available.
     *               - 'expiration_date': The expiration date for unlimited invoices, or the current date.
     */
    public function getInvoicesAvailable(string $companyId)
    {
        $now = Carbon::now();

        $baseQuery = DB::table('memberships as m')
            ->join('membership_has_modules as mm', 'm.id', '=', 'mm.membership_id')
            ->join('membership_submodules as mms', 'mm.id', '=', 'mms.membership_has_modules_id')
            ->where('m.company_id', '=', $companyId)
            ->where('m.payment_status', Membership::PAYMENT_STATUS_APPROVED)
            ->where('m.payment_method', Membership::PAYMENT_METHOD_PAYU)
            ->whereIn('mms.sub_module_id', MembershipHasModules::SUBMODULES_INVOICE_WITH_INVENTORY_ADJUSTMENT)
            ->orderBy('m.purchase_date');

        $unlimitedDocuments = (clone $baseQuery)
            ->where('mms.sub_module_id', MembershipHasModules::SUBMODULES_INVOICE_UNLIMITED)
            ->where('mms.expiration_date', '>', $now);

        $hasUnlimited = $unlimitedDocuments->exists();
        if ($hasUnlimited) {
            $date = $unlimitedDocuments->first()->expiration_date;
            if ($date < $now)
                $this->updateInformationIsBillingUs($companyId);

            return [
                'number_invoice' => 0,
                'is_unlimited' =>  $hasUnlimited,
                'expiration_date' =>  $date
            ];
        }

        $numberElectronicDocumentsUsedInvoice = ($this->invoiceService->getNumberInvoicesCreated($companyId)['data'] ?? 0);

        $moduleInvoices = (clone $baseQuery)
            ->get();

        $totalDocumentsPurchased = $moduleInvoices->sum('total_invoices');
        $totalDocumentsRemaining = $moduleInvoices->sum('remaining_invoices');

        $totalDocumentsAvailableInvoice = $totalDocumentsPurchased - $numberElectronicDocumentsUsedInvoice;
        $number_invoice = ($totalDocumentsAvailableInvoice == $totalDocumentsRemaining) ? $totalDocumentsRemaining : $totalDocumentsAvailableInvoice;

        if ($number_invoice <= 0)
            $this->updateInformationIsBillingUs($companyId);

        return [
            'number_invoice' => $number_invoice,
            'is_unlimited' =>  $hasUnlimited,
            'expiration_date' =>  $now
        ];
    }


    /**
     * Updates the count of used electronic documents for a company.
     *
     * This method checks if the company has any active membership modules
     * with available electronic documents. If a module with unlimited
     * documents is found, it increments the total invoices count. If
     * limited documents are available, it decrements the remaining invoices.
     *
     * @param string $companyId The ID of the company to update.
     * @return bool Returns true if the electronic document count was successfully updated, false otherwise.
     */
    public function counterUsedElectronicDocuments(string $companyId): bool
    {
        $now = Carbon::now();

        $baseQuery = DB::table('memberships as m')
            ->join('membership_has_modules as mm', 'm.id', '=', 'mm.membership_id')
            ->join('membership_submodules as mms', 'mm.id', '=', 'mms.membership_has_modules_id')
            ->where('m.company_id', '=', $companyId)
            ->where('m.payment_status', 'APPROVED')
            ->where('m.payment_method', 'PAYU')
            ->whereIn('mms.sub_module_id', MembershipHasModules::SUBMODULES_INVOICE_WITH_INVENTORY_ADJUSTMENT)
            ->orderBy('m.purchase_date');

        $unlimitedDocuments = (clone $baseQuery)
            ->where('mms.sub_module_id', MembershipHasModules::SUBMODULES_INVOICE_UNLIMITED)
            ->where('mms.expiration_date', '>', $now);

        if ($unlimitedDocuments->exists()) {
            MembershipSubModule::find($unlimitedDocuments->first()->id)->increment('total_invoices', 1);
            return true;
        }

        $moduleInvoices = (clone $baseQuery)
            ->where('mms.remaining_invoices', '>', 0);

        if ($moduleInvoices->exists()) {
            $module = $moduleInvoices->first();
            $remainingInvoices = MembershipSubModule::where('id', $module->id)->value('remaining_invoices');
            if ($remainingInvoices > 0) {
                MembershipSubModule::where('id', $module->id)->decrement('remaining_invoices', 1);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getActiveMembershipsByCompany(string $companyId)
    {
        $company = $this->companyModel::find($companyId);
        return MembershipCollectionResource::collection(
            $company->memberships()
                ->where('is_active', true)
                ->with('modules.membershipSubmodules')->get()
        );
    }

    public function updateInformationBilling(string $companyId, array $request)
    {
        $this->companyModel->find($companyId)->update($request);
        $this->fiscalResponsibility->storeMany($request['fiscal_responsibilities'], auth()->user()->company_id);
        if (array_key_exists('companies_foreign_exchange', $request)) {
            foreach ($request['companies_foreign_exchange'] as $value) {
                $this->CompanyForeignExchangeEloquent->store([
                    'id' => $value['id'] ?? null,
                    'foreign_exchange_id' => $value['foreign_exchange_id'],
                    'is_active' => $value['is_active'],
                    'company_id' => $companyId
                ]);
            }
        }
        return $this->getCompanyInfo($companyId);
    }

    public function getDomain(string $company_id)
    {
        return $this->companyModel->find($company_id)->domain ?? null;
    }

    public function getNamesCompanies(array $ids)
    {
        return $this->companyModel::whereIn('id', $ids)->select('id', 'name')->get();
    }

    /**
     * Update is_billing_us attribute
     * @param $companyId
     * @return CompanyResource
     * @throws GuzzleException
     */
    public function updateInformationIsBillingUs(string $companyId)
    {
        $company = $this->companyModel->find($companyId);

        $company->update([
            "is_billing_us" => false
        ]);

        return $this->getCompanyInfo($companyId);
    }

    /**
     * get Companies Administration
     * @return Illuminate\Http\Resources\Json\JsonResource
     */
    public function getCompaniesAdministration()
    {
        $dataAdmnistration = $this->membershipsEloquent->getMembershipsAllCompaniesAdministration();
        $companiesResponse = collect(CompanyHelper::getInformationCompanies($dataAdmnistration));
        $countActive = 0;
        $companiesResponse = $companiesResponse->filter(function ($item) use (&$countActive) {
            if (count($item["modules"]) == 0 || $item["email"] == '') {
                return false;
            }
            collect($item["modules"])->each(function ($module) use (&$countActive, &$isActive) {
                if (collect($module)["is_active"] == true && !$isActive) {
                    $countActive++;
                    $isActive = true;
                }
            });
            return true;
        })->values();

        $totalCompanies = $companiesResponse->count();
        $result = [
            'total' => $totalCompanies,
            'total_active' => $countActive,
            'companies' => $companiesResponse->sortByDesc("last_plan_purchased")->values(),
        ];
        return $result;
    }

    /**
     * @param $companyId
     * @param UpdateCompanyAccountCreated $request
     * @return CompanyResource
     * @throws GuzzleException
     */
    public function updateAccountCreated(string $companyId, UpdateCompanyAccountCreated $request): CompanyResource
    {
        $company = $this->companyModel->find($companyId);
        $company->update($request->validated());
        $company->touch();
        return $this->getCompanyInfo($companyId);
    }

    /**
     * Retrieves a client's company logo based on the provided data
     *
     * @param array $data The data containing email and domain for the client
     * @return array An array containing the URL of the company logo and the company name
     */
    public function getClientCompanyLogo(array $data): array
    {
        $client = isset($data['email']) ? $this->clientModel::where('email', $data['email'])->first() : null;

        if (isset($client->companies) && isset($data['domain'])) {
            $companyId =  $client->companies->where('domain', $data['domain'])->first()->id ?? null;
        }

        $website = $this->websiteService->getCompanyLogoByDomain($data);
        $companyInfo = $this->getCompanyInfo($companyId ?? null);

        return [
            "url" => $website['url'] ?? null,
            "company_name" => $companyInfo['name'] ?? null
        ];
    }

    /**
     * @param $id
     * @param UpdateCompanyMinimunDataRequest $request
     * @return CompanyResource
     * @throws GuzzleException
     */
    public function updateCompanyMinimunData($id, UpdateCompanyMinimunDataRequest $request): CompanyResource
    {
        $company = $this->companyModel->find($id);
        $company->update($request->only(['name', 'document_type', 'document_number', 'person_type']));
        return $this->getCompanyInfo($id);
    }

    /**
     * This function returns the error message for login in case you have no memberships or no active memberships.
     *
     * @param string $companyId
     * @return string
     */
    public function getMembershiploginErrorMessage(string $companyId): string
    {
        $memberships = Membership::with(['modules.membershipSubmodules'])
            ->where('company_id', $companyId)
            ->get();

        if ($memberships->isEmpty() || !$memberships->contains('payment_status', Membership::PAYMENT_STATUS_APPROVED)) {
            return 'The company has not purchased modules';
        }

        if (!$memberships->contains('is_active', true)) {
            return 'The company has no active modules';
        }

        $hasNonInvoiceSubmodules = $memberships->flatMap(function ($membership) {
            return $membership->modules;
        })->flatMap(function ($module) {
            return $module->membershipSubmodules;
        })->contains(function ($submodule) {
            return $submodule->is_active && !in_array(
                $submodule->sub_module_id,
                MembershipSubModule::DEACTIVABLE_SUB_MODULES
            );
        });

        if (!$hasNonInvoiceSubmodules) {
            $invoices = $this->getInvoicesAvailable($companyId);
            $hasInvoiceAccess = ($invoices['number_invoice'] ?? 0) > 0 || ($invoices['is_unlimited'] ?? false);

            if (!$hasInvoiceAccess) {
                return 'The company has no electronic documents or active modules';
            }
        }

        return '';
    }

    /**
     * Return an array of all companies' id
     *
     * @return array
     */
    public function getAllCompanies(): array
    {
        return $this->companyModel::pluck('id')->toArray();
    }

    /**
     * Modify the collection of attachments by adding the URL of the logo
     * @param Collection $request The collection of attachments
     * @return Collection The modified collection of attachments
     */
    public function getUrlAttachment(Collection $request)
    {
        foreach ($request->all() as $value) {
            $value['logo_url'] = null;
            if ($value['bucket_id']) {
                $bucketDetails = BucketHelper::getUrl($value['bucket_id']);
                $value['logo_url'] = $bucketDetails['url'];
            }
        }
        return $request;
    }

    /**
     * Get company information by billing
     * @param string $companyId
     * @return CompanyCompanyResource
     */
    public function getInformationByBilling(string $companyId): CompanyCompanyResource
    {
        $company = $this->companyModel::with('fiscalResponsibilities', 'attachments', 'prefixes', 'ciius')->find($companyId);
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
                'fields' => [],
                'multiple_record' => false
            ],
            [
                'model' => 'FiscalResponsibility',
                'constraints' => [],
                'fields' => [],
                'multiple_record' => true
            ],
            [
                'model' => 'DocumentType',
                'constraints' => [
                    [
                        'field' => 'id',
                        'operator' => '=',
                        'parameter' => $company->document_type,
                    ]
                ],
                'fields' => [],
                'multiple_record' => false
            ]
        ];
        $company['attachments'] = $this->getUrlAttachment($company->attachments);
        $utils = UtilsHelper::dynamicResource($dynamicResource);
        return CompanyCompanyResource::make($company)->additional(['utils' => $utils]);
    }
}
