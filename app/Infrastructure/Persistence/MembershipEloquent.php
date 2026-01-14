<?php


namespace App\Infrastructure\Persistence;

use App\Enums\PaymentMethodsEnum;
use App\Infrastructure\Formulation\InvoiceHelper;
use App\Helpers\MembershipCalculateHelper;
use App\Http\Resources\MembershipInvoice\ElectronicMembershipInvoiceResource;
use App\Http\Resources\MembershipCollectionResource;
use App\Http\Resources\MembershipResourceCollection;
use App\Infrastructure\Formulation\MembershipHelper;
use App\Infrastructure\Services\InventoryService;
use App\Infrastructure\Formulation\UtilsHelper;
use App\Infrastructure\Services\InvoiceService;
use App\Infrastructure\Services\NotificationsService;
use App\Models\CancelModulesDetail;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Models\MembershipPurchaseProcess;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Services\PayService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MembershipEloquent
{
    /**
     * @var Membership
     */
    private $model;

    /**
     * @var UserEloquent
     */
    private $userEloquent;

    /**
     * @var MembershipDetailEloquent
     */
    private $membershipDetailEloquent;

    private $payService;
    private $membershipHasModuleModel;
    private $companyModel;
    private $payTransactionEloquent;
    private $prefixEloquent;
    private $cancelModulesDetailModel;
    private $invoiceService;
    private $membershipSubModuleModel;
    private $dataBinnacleModules;
    private $notificationsService;
    private $inventoryService;

    public function __construct()
    {
        $this->model = new Membership();
        $this->membershipHasModuleModel = new MembershipHasModules();
        $this->userEloquent = new UserEloquent();
        $this->membershipDetailEloquent = new MembershipDetailEloquent();
        $this->payService = new PayService();
        $this->companyModel = new Company();
        $this->payTransactionEloquent = new PayTransactionEloquent();
        $this->prefixEloquent = new PrefixEloquent();
        $this->cancelModulesDetailModel = new CancelModulesDetail();
        $this->invoiceService = new InvoiceService();
        $this->membershipSubModuleModel = new MembershipSubModule();
        $this->notificationsService = new NotificationsService();
        $this->inventoryService = new InventoryService();
    }

    /**
     * @param string $company_id
     * @return array
     * @throws GuzzleException
     */
    public function getMembershipStatus(string $company_id): array
    {
        $allMemberships = $this->getAllMembershipsByCompany($company_id);
        $activeMembership = collect($allMemberships)->firstWhere('is_active', true);
        return [
            'active_membership' => $activeMembership,
            'company_memberships' => $allMemberships
        ];
    }

    /**
     * @param string $companyId
     * @return MembershipResourceCollection
     * @throws GuzzleException
     */
    public function getAllMembershipsByCompany(string $companyId): MembershipResourceCollection
    {
        $modules = MembershipHelper::getAllMembershipModules()['modules'];
        return MembershipResourceCollection::make($this->model->query()->where('company_id', $companyId)->get())->using([
            'modules' => $modules
        ]);
    }

    /**
     * @return Builder|Membership|Model|object|null
     */
    public function getLastMembership()
    {
        return $this->model->query()->latest('expiration_date')->first();
    }

    /**
     * @param $request
     * @param string $companyId
     * @param bool $isFirstMembership
     * @throws GuzzleException
     */
    public function storeMembership(array $request)
    {
        $isFirstPayment = MembershipCalculateHelper::validateIfIsFirstPaymentOrFreeMembership($request['company_id']);

        $modules = $request['modules'];

        if ($request['option_pay'] == 'PAY_WITH_TOKEN') {
            $request['payu_data'] = $this->payService->getDataCompanyPayu($request['company_id'])['data']['payu_data'];
            $payTransaction = $this->model::with('payTransaction')
                ->where('company_id', $request['company_id'])
                ->orderBy('created_at')
                ->first();
            $invoice = collect($payTransaction)["pay_transaction"]["json_invoice"];
            if (isset(json_decode($invoice, true)["additional_customer_data"])) {
                $request["additional_customer_data"] = json_decode($invoice, true)["additional_customer_data"];
            }
        }

        if (count($modules) == 0 && $request['users_quantity'] > 0 && $isFirstPayment)
            return "Can't purchase users if you haven't active a membership";

        $allModulesUtils = MembershipHelper::getAllMembershipModules()['modules'];
        $initialDate = Carbon::now()->toDateString();
        $purchaseDate = Carbon::now()->toDateTimeString();
        $isFrequentPayment = count($modules) > 0 && !in_array($request['option_pay'], ['PAY_WITHOUT_TOKEN', 'PAY_PSE']);
        $price = $this->calculateTotalPriceMembership($request, $modules, $allModulesUtils);

        $membership = $this->model::create([
            'purchase_date' => $purchaseDate,
            'initial_date' => $initialDate,
            'is_active' => false,
            'is_frequent_payment' => $isFrequentPayment,
            'company_id' => $request['company_id'],
            'price' => round($price, 2),
            'is_first_payment' => $isFirstPayment,
            'expiration_date' => Carbon::now()->addMonths($this->model::EXPIRATION_DATE_MEMBERSHIP),
            'payment_method' => $this->model::PAYMENT_METHOD_PAYU
        ]);

        $membership = $this->model::find($membership->id);

        // validate if only purchases pages or users
        if (count($modules) > 0)
            $this->membershipDetailEloquent->storeMembershipHasModules(array_merge($modules, $this->membershipHasModuleModel::FREE_MODULES), $membership, $allModulesUtils);

        $response = $this->processPay($request, $price);
        if (isset($response['statusCode']) && $response['statusCode'] == 400 || (isset($response['data']['message']) ?? $response['data']['message'] == 'Service Unavailable')) {
            $response['data']['transactionResponse']['message'] = $response['message'];
            unset($response['message']);
            return $response;
        }
        $response = $response['data'];
        if ($response['transactionResponse']['state'] == $this->model::PAYMENT_STATUS_APPROVED) {
            MembershipCalculateHelper::addFreeUser($request['company_id']);
            MembershipPurchaseProcess::where('company_id', $request['company_id'])
                ->where('is_payment', false)
                ->update([
                    'reference_id' => $membership->id,
                    'is_payment' => true,
                ]);
            $membership->is_active = true;
            $membership->is_frequent_payment = $isFrequentPayment;
            $membership->payment_status = $response['transactionResponse']['state'];
            $membership->save();

            if (count($modules) > 0)
                $this->membershipDetailEloquent->activeModulesByMembershipId($membership);

            $company = $this->companyModel::find($request['company_id']);
            $company->users_available += $request['users_quantity'];
            $company->save();

            if (isset($currentModuleWebsite)) {
                self::deactivateLastWebsite($currentModuleWebsite);
            }

            self::sendMailWithNotification($request, $company, $price, $membership, $allModulesUtils);
            //self::electronicMembershipInvoice($modules, $request, $membership->id, $allModulesUtils, $price);

        } else {
            $membership->payment_status = $response['transactionResponse']['state'];
            $membership->save();
        }

        $this->payTransactionEloquent->storePayTransaction(
            $request['company_id'],
            $response['transactionResponse']['transactionId'] ?? Str::uuid()->toString(),
            $response['transactionResponse']['state'],
            $request['users_quantity'],
            0,
            $request,
            $membership->id,
            $membership->is_first_payment,
        );

        return $response;
    }

    public function sendMailWithNotification($request, $company, $price, $membership, $allModulesUtils)
    {
        $membershipsActives = self::getDetailsMembership($request['company_id']);
        $user = $this->userEloquent->getUserByCompanyId($company->id, $company->document_number);
        $documents = MembershipHelper::getAllDocumentTypes();
        $moduelsActives = collect($membershipsActives["active_memberships"])->where('id', $membership->id)->first();
        $modulesNames = [];
        if (MembershipCalculateHelper::countValidModules($membershipsActives["active_memberships"]) > 1) {
            $this->membershipDetailEloquent->createFreeMembershipModule($this->membershipHasModuleModel::PLANNING_ORGANIZATION, $membership, $allModulesUtils);
        }
        collect($moduelsActives["modules"])->each(function ($item) use (&$modulesNames) {
            if ($item["membership_modules_id"] == MembershipHasModules::MODULE_WAREHOUSES || $item["membership_modules_id"] == MembershipHasModules::MODULE_DIGITAL_SALES) {
                array_push($modulesNames, [
                    "name" => $item["nameModule"]
                ]);
            } elseif (in_array($item["membership_modules_id"], [MembershipHasModules::MODULE_INVOICE_ID, MembershipHasModules::MODULE_WEB_SITE])) {
                collect($item['membership_submodules'])->each(function ($submodule) use (&$modulesNames) {
                    array_push(
                        $modulesNames,
                        [
                            "name" => $submodule["nameModule"]
                        ]
                    );
                });
            }
        });
        $documment = isset($company->document_type) ? collect($documents)->where('id', $company->document_type)->first() : null;
        try {
            $data = [
                'email_client' => isset($user->email) ? $user->email : null,
                'document_number' => $company->document_number,
                'document_type' => $documment ? $documment["name"] : null,
                'purchase_date' => Carbon::now()->format('d/m/Y H:i:s'),
                'module' => $modulesNames,
                "total" => $price,
                'company_id' => $request['company_id'],
                'notification' => [
                    'type' => $this->model::NOTIFICATION_TYPE,
                    'module_notification_id' => $this->model::MODULE_PAYMENT_PLANS,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'consecutive' => 'Compra de membresía',
                    'reference' => $this->companyModel::COMPANY_CCXC,
                    'user_id' => $this->companyModel::COMPANY_CCXC,
                    'company_id' => $this->companyModel::COMPANY_CCXC,
                    'type_notification_id' => $this->model::NOTIFICATION_TYPE_MEMBERSHIP_PURCHASE,
                    'state_notification_id' => $this->model::STATE_NOTIFICATION_SEND,
                    'description' => "Email del Cliente: " . (isset($user->email) ? $user->email : 'No disponible') . " | " .
                        "Número de Documento: " . $company->document_number . " | " .
                        "Tipo de Documento: " . ($documment ? $documment["name"] : 'No disponible') . " | " .
                        "Fecha de la Compra: " . Carbon::now()->format('d-m-Y') . " | " .
                        "Artículos Comprados: " . implode(", ", array_column($modulesNames, 'name')) . " | " .
                        "Total: $" . number_format($price, 2, ',', '.'),
                ]
            ];
            $this->notificationsService->sendEmailSaleMembership($data);
        } catch (\Exception $e) {
            \Log::info("Error on storeMembership send email purchases: " . $e->getMessage());
        }
    }


    /**
     * Deactivates the last website module and its submodules if the number of modules is greater than the count of free modules.
     * Otherwise, deactivates the entire website, its modules, and their submodules.
     *
     * @param mixed $currentModuleWebsite The actual module website object.
     * @return void
     */
    public function deactivateLastWebsite($currentModuleWebsite): void
    {
        $countFreeModules = collect($this->membershipHasModuleModel::FREE_MODULES)->count();
        $actualCountModules = $currentModuleWebsite->modules()->count();
        $diffModules = $actualCountModules - $countFreeModules;
        if ($diffModules > 1) {
            $currentModuleWebsite->modules->first()->update(['is_active' => false]);
            $currentModuleWebsite->modules->first()->membershipSubmodules()->update(['is_active' => false]);
        } else {
            $currentModuleWebsite->update(['is_active' => false]);
            $currentModuleWebsite->modules()->update(['is_active' => false]);
            $currentModuleWebsite->modules->first()->membershipSubmodules()->update(['is_active' => false]);
        }
    }

    public function toValidateIfChargeNewMembership(array $request)
    {
        $response['statusCode'] = 200;
        $response['message'] = 'Success validation';
        $countFreeMembership = $this->model::where('company_id', $request['company_id'])
            ->where('is_active', true)
            ->where('payment_method', $this->model::PAYMENT_METHOD_FREE)
            ->count();
        $freeModulesId = collect($this->membershipHasModuleModel::FREE_MODULES)->pluck('id')->toArray();

        if ($countFreeMembership == 1) {

            $membership = $this->model::where('company_id', $request['company_id'])
                ->where('payment_method', $this->model::PAYMENT_METHOD_FREE)
                ->with([
                    'modules' => function ($query) use ($freeModulesId) {
                        $query->whereNotIn('membership_modules_id', $freeModulesId)->with('membershipSubmodules');
                    }
                ])
                ->first();

            foreach ($membership->modules as $module) {
                $moduleFound = false;
                foreach ($request['modules'] as $moduleRequest) {
                    if ($moduleRequest['id'] == $this->membershipHasModuleModel::MODULE_INVOICE_ID) {
                        return $response;
                    }
                    if ($module->membership_modules_id == $moduleRequest['id']) {
                        $moduleFound = true;
                        if (
                            array_key_exists('sub_modules', $moduleRequest) && isset($moduleRequest['sub_modules'][0]['id'])
                            && $moduleRequest['sub_modules'][0]['id'] != $module->membershipSubmodules[0]->sub_module_id
                        ) {
                            $response['statusCode'] = 400;
                            $response['message'] = 'The membership is different to the free current membership (Submodules are different)';
                            return $response;
                        }
                    }
                }
                if (!$moduleFound) {
                    $response['statusCode'] = 400;
                    $response['message'] = 'The membership is different to free the current membership (Modules are different)';
                    return $response;
                }
            }
        }
        return $response;
    }


    public function processPay(array $request, float $price)
    {
        $values = [
            "TX_VALUE" => [
                "value" => round($price, 2),
                "currency" => "COP"
            ],
            "TX_TAX" => [
                "value" => 0,
                "currency" => "COP"
            ],
            "TX_TAX_RETURN_BASE" => [
                "value" => 0,
                "currency" => "COP"
            ]
        ];
        $response = false;
        switch ($request['option_pay']) {
            case 'PAY_AND_CREATE_TOKEN':
                $payuData = $request['payu_data'];
                $payuData['transaction']['order']['additionalValues'] = $values;
                $response = $this->payService->payAndCreateToken($payuData);
                break;
            case 'PAY_WITHOUT_TOKEN':
                $payuData = $request['payu_data'];
                $payuData['transaction']['order']['additionalValues'] = $values;
                unset($payuData['transaction']['creditCard']['payerId']);
                unset($payuData['transaction']['creditCard']['paymentMethod']);
                unset($payuData['transaction']['creditCard']['identificationNumber']);
                $response = $this->payService->payWithoutToken($payuData);
                break;
            case 'PAY_WITH_TOKEN':
                $response = $this->payService->payWithToken($values, $request["company_id"]);
                break;
            case 'PAY_PSE':
                $payuData = $request['payu_data'];
                $payuData['transaction']['order']['additionalValues'] = $values;
                $response = $this->payService->payPse($payuData);
                break;
            case 'CREATE_TOKEN':
                $dataToken['creditCardToken'] = $request['payu_data']['transaction']['creditCard'];
                $dataToken['language'] = 'es';
                $dataToken['command'] = 'CREATE_TOKEN';
                $response = $this->payService->createToken($dataToken);
                break;
        }
        return $response;
    }

    /**
     * It calculates the total price of the membership.
     *
     * @param array $request
     * @param array $modulesRequest
     * @param array $allModules
     *
     * @return float
     */
    public function calculateTotalPriceMembership(array $request, array $modulesRequest, array $allModulesUtils): float
    {
        $price = 0;
        if ($request['users_quantity'] > 0)
            $price += ($this->model::PRICE_USER_MEMBERSHIP * $request['users_quantity']);

        if (count($request['modules']) > 0)
            $price += $this->sumPriceOfModules($modulesRequest, $allModulesUtils);
        return $price;
    }

    /**
     * It calculates the price of the extra pages.
     *
     * @param array $request array request array of the form:
     * @param array $allModules array allModules This is an array of all the modules and submodules that are available.
     *
     * @return int The price of the extra pages.
     */
    public function sumPriceAdditionalPages(array $request, array $allModules)
    {
        $moduleWeb = collect(collect($allModules)->firstWhere('id', $this->membershipHasModuleModel::MODULE_WEB_SITE)['sub_modules']);
        $priceDesignBase = $moduleWeb->firstWhere('id', 5)['price'];
        $priceExtraPage = $moduleWeb->firstWhere('id', 7)['price'];
        $priceMaintenanceExtraPage = $moduleWeb->firstWhere('id', 7)['price_maintenance_year'];

        return (($priceDesignBase
            - ($priceExtraPage * ($request['pages_quantity'] + 1))
            + ($priceMaintenanceExtraPage)
        )
            * ($request['pages_quantity']));
    }

    /**
     * It sums the price of the modules and submodules of a membership
     *
     * @param array $request array of the request
     * @param array $modulesRequest array of modules and submodules
     * @param array $allModules array of all modules
     * @param bool $isImmediatePurchase boolean
     * @param bool $isUpgradeWebSite boolean
     *
     * @return float The sum of the prices of the modules purchase.
     */
    public function sumPriceOfModules(array $modulesRequest, array $allModulesUtils): float
    {
        $countModules = collect($modulesRequest)->where('id', '!=', 5)->groupBy('id')->count();
        return collect($modulesRequest)->reduce(function ($carry, $module) use ($allModulesUtils, $countModules) {
            if (isset($module['sub_modules']) && count($module['sub_modules']) > 0) {
                return $carry + (collect($module['sub_modules']))->map(function ($subModule) use ($module, $allModulesUtils) {

                    $moduleUtils = collect(collect($allModulesUtils)->firstWhere('id', $module['id'])['sub_modules'])
                        ->where('id', $subModule['id'])->first();

                    $subModule['price_purchase'] = $moduleUtils['price_year'];
                    if (isset($subModule['expiration_date']) && $subModule['expiration_date'] == $this->model::EXPIRATION_DATE_SEMESTER)
                        $subModule['price_purchase'] = $moduleUtils['price_semester'];

                    if ($module['id'] == $this->membershipHasModuleModel::MODULE_WEB_SITE)
                        $subModule['price_purchase'];

                    if ($module['id'] == $this->membershipHasModuleModel::MODULE_INVOICE_ID)
                        $subModule['price_purchase'] = $moduleUtils['base_price'];

                    return $subModule;
                })->sum('price_purchase');
            }
            $priceTotal = $module['id'] == $this->membershipHasModuleModel::PLANNING_ORGANIZATION_ID && $countModules > 1 ? 0 : collect($allModulesUtils)->firstWhere('id', $module['id'])['price_year'];
            return $carry + $priceTotal;
        }, 0);
    }

    /*
     * @param $request
     */
    public function getMembershipById($request)
    {
        return $this->model->query()->findOrFail($request['membership_id']);
    }

    public function payMembership(array $request)
    {
        return $this->model::find($request['id'])
            ->update([
                'transaction_id' => $request['transaction_id'],
                'purchase_date' => $request['purchase_date'] ?? null,
                'initial_date' => $request['purchase_date'] ?? null,
                'expiration_date' => isset($request['purchase_date']) ? Carbon::parse($request['purchase_date'])->addMonth()->getTimestamp() : null,
                'is_active' => isset($request['purchase_date']) ? 1 : 0
            ]);
    }

    public function activeMembership($membershipId)
    {
        $membership = Membership::find($membershipId);
        $membership->is_active = true;
        $membership->payment_status = 'APPROVED';
        $membership->save();

        $this->membershipDetailEloquent->activeModulesByMembershipId($membership);
    }

    public function addUsersMembership(string $companyId, $usersQuantity)
    {
        $company = Company::find($companyId);
        $company->users_available += $usersQuantity;
        $company->save();
    }

    public function addPagesMembership(string $companyId, $pagesQuantity)
    {
        $company = Company::find($companyId);
        $company->pages_available += $pagesQuantity;
        $company->save();
    }

    /**
     * Cancel recurrent payment for any module o modules
     * @param array $data
     * @return MembershipCollectionResource
     */
    public function cancelModulesMemberships(array $data): MembershipCollectionResource
    {
        $membership_id = '';
        foreach ($data['modules_id'] as $module_id) {
            $module = $this->membershipHasModuleModel::find($module_id);
            $membership_id = $module->membership_id;

            if ($module->is_frequent_payment) {
                foreach ($module->membershipSubmodules as $membershipSubmodule) {
                    $membershipSubmodule->update([
                        'is_frequent_payment' => false,
                    ]);
                }

                $module->update([
                    'is_frequent_payment' => false,
                ]);

                $membership = $this->model::find($membership_id);

                $this->cancelModulesDetailModel::create([
                    'membership_has_modules_id' => $module_id,
                    'reason' => $data['reason_cancellation'],
                    'company_id' => $membership->company_id,
                    'membership_id' => $membership_id,
                ]);

                $modules_membership = $membership->modules()
                    ->where('is_frequent_payment', true)
                    ->whereNotIn('membership_modules_id', collect($this->membershipHasModuleModel::FREE_MODULES)->pluck('id')->toArray())
                    ->get()->toArray();

                if (count($modules_membership) == 0)
                    $this->cancelAllModulesMemberships($membership);
            }
        }
        return MembershipCollectionResource::make($this->model::find($membership_id));
    }

    /**
     * When the membership has no modules with recurrent payment, it cancels the membership recurrent payment.
     * @param Membership $membership
     * @return void
     */
    private function cancelAllModulesMemberships(Membership $membership): void
    {
        $membership->update([
            'is_frequent_payment' => false,
        ]);

        $membership->modules->each(function ($module) {
            $module->update([
                'is_frequent_payment' => false,
            ]);
            $module->membershipSubmodules->each(function ($membershipSubmodule) {
                $membershipSubmodule->update([
                    'is_frequent_payment' => false,
                ]);
            });
        });
    }

    /**
     * It creates a PDF invoice for a membership.
     *
     * @param array $modules array of modules that the membership has
     * @param array $request The request object.
     * @param string $membershipId The id of the membership that is being created.
     * @param string $allModulesUtils All modules utils
     * @param string $price total membership price
     *
     * @return void the membership data.
     * @throws GuzzleException
     */
    public function electronicMembershipInvoice(array $modules, array $request, string $membershipId, array $allModulesUtils, $price)
    {
        $company = isset($request["payer"]) ? $request["payer"] : $request["payu_data"]["transaction"]["payer"];
        $company["additional"] = $request["additional_customer_data"];
        $paymentMethod = ($request['payu_data']['transaction']['paymentMethod'] == PaymentMethodsEnum::PSE)
            ? PaymentMethodsEnum::DESCRIPTIONS[PaymentMethodsEnum::PSE]
            : (
                (isset($request['additional_customer_data']['card_type']) && $request['additional_customer_data']['card_type'] === PaymentMethodsEnum::CREDIT_CARD)
                ? PaymentMethodsEnum::DESCRIPTIONS[PaymentMethodsEnum::CREDIT_CARD]
                : PaymentMethodsEnum::DESCRIPTIONS[PaymentMethodsEnum::DEBIT_CARD]
            );

        $queryForUtils = [
            [
                'model' => 'PaymentMethod',
                'constraints' => [
                    [
                        'field' => 'name',
                        'operator' => '=',
                        'parameter' => $paymentMethod
                    ]
                ],
                'fields' => ['id', 'name'],
                'multiple_record' => false
            ],
            [
                'model' => 'PaymentType',
                'constraints' => [
                    [
                        'field' => 'name',
                        'operator' => '=',
                        'parameter' => 'Contado'
                    ]
                ],
                'fields' => ['id', 'name'],
                'multiple_record' => false
            ],
            [
                'model' => 'ForeignExchange',
                'constraints' => [
                    [
                        'field' => 'name',
                        'operator' => '=',
                        'parameter' => 'Peso colombiano'
                    ]
                ],
                'fields' => ['id', 'name'],
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
        ];
        $utilsData = UtilsHelper::dynamicResource($queryForUtils);
        $company["additional"]["fiscal_responsibilities"] = collect($request["additional_customer_data"]["fiscal_responsibilities"])->map(function ($item) use ($utilsData) {
            $item = collect($item);
            $result = collect($utilsData["fiscal_responsibilities"])
                ->firstWhere('id', $item['id']);
            if ($result) {
                $result['id'] = (string) $result['id'];
            }
            return $result;
        });
        $moduleDetails = [];
        collect($modules)->map(function ($item) use ($allModulesUtils, $membershipId, &$moduleDetails) {
            $module = collect($allModulesUtils)->firstWhere('id', $item['id']);
            if (isset($item['sub_modules'])) {
                collect($item['sub_modules'])->map(function ($subitem) use ($item, $module, &$moduleDetails) {
                    $submodule = collect($module['sub_modules'])->firstWhere('id', $subitem['id']);
                    array_push($moduleDetails, $submodule["unique_product_id"]);
                });
            } else {
                array_push($moduleDetails, $module["unique_product_id"]);
            }
            return $item;
        });
        $company["products"] = $this->inventoryService->getServicesByUniqueProductId($moduleDetails);
        $ccxc = Company::findOrFail(Company::COMPANY_CCXC);
        $company['total_invoice'] = $price;
        $company['total_sale_value'] = $price;
        $company['total_sale'] = $price;
        $company['total'] = $price;
        $prefix = $this->prefixEloquent->getPrefix(Company::COMPANY_CCXC, ['INVOICE']);
        $filteredPrefix = collect(collect($prefix)->filter(function ($value, $key) {
            $date = Carbon::now()->timestamp;
            return $value["final_validity"] >= $date && $value["physical_store"];
        }))->first();
        if (env('APP_DEBUG') == true) {
            $company["prefix"] = '73449052-3816-3ca9-8c02-55642a3a464b';
            $company["prefix_id_name"] = 'SETP';
        } else {
            $company["prefix"] = $filteredPrefix['id'];
            $company["prefix_id_name"] = $filteredPrefix['prefix'];
        }
        $company["ccxc"] = $ccxc;
        $membershipData = ElectronicMembershipInvoiceResource::toArray($company, $utilsData);
        $invoice = InvoiceHelper::createMembershipInvoice($membershipData, Company::COMPANY_CCXC, Company::COMPANY_CCXC);
        if ($invoice['statusCode'] == 200 && isset($invoice["data"][0])) {
            $this->model->where('id', $membershipId)->update(['invoice_pdf' => $invoice["data"][0]['invoice_pdf_url'], 'invoice_id' => $invoice["data"][0]['id'], 'email_send' => true]);
        }
    }

    /**
     * Get the number of pages available for a company
     *
     * @param string $companyId uuid
     *
     * @return int pages available for a company.
     */
    public function getPagesAvailable(string $companyId): int
    {
        return $this->companyModel->find($companyId)->pages_available;
    }

    /**
     * Filter membership in active and inactive by company_Id
     * @param string $company_id uuid
     * @return array
     * @throws GuzzleException
     */
    public function getDetailsMembership(string $company_id): array
    {
        $baseQuery = $this->model::with(['payTransaction'])
            ->where('company_id', $company_id)
            ->where('payment_status', $this->model::PAYMENT_STATUS_APPROVED);

        $membershipsActives = (clone $baseQuery)
            ->with([
                'modules' => function ($query) {
                    $query->where('is_active', true)
                        ->with('membershipSubmodules', function ($query) {
                            $query->where('is_active', true);
                        });
                }
            ])
            ->where('is_active', true)
            ->get();

        $membershipsInactive = (clone $baseQuery)
            ->with(['modules'])
            ->where('is_active', false)
            ->get();

        $membershipsInactiveModules = (clone $baseQuery)
            ->with([
                'modules' => function ($query) {
                    $query->where('is_active', false);
                }
            ])
            ->where('is_active', true)
            ->whereHas('modules', function ($query) {
                $query->where('is_active', false);
            })
            ->get();

        $membershipsInactiveSubmodules = (clone $baseQuery)
            ->with([
                'modules' => function ($query) {
                    $query->where('is_active', true)
                        ->with([
                            'membershipSubmodules' => function ($subQuery) {
                                $subQuery->where('is_active', false);
                            }
                        ])
                        ->whereHas('membershipSubmodules', function ($subQuery) {
                            $subQuery->where('is_active', false);
                        });
                }
            ])
            ->where('is_active', true)
            ->whereHas('modules', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('membershipSubmodules', function ($subQuery) {
                        $subQuery->where('is_active', false);
                    });
            })
            ->get();


        $inactiveMemberships = $membershipsInactive
            ->push($membershipsInactiveModules)
            ->push($membershipsInactiveSubmodules)
            ->flatten(1)
            ->values();

        $inactiveMemberships = $this->setFalseIsActiveMemberships($inactiveMemberships);

        $memberships = $membershipsActives
            ->push($inactiveMemberships)
            ->flatten(1)
            ->values()
            ->sortBy('purchase_date');

        if ($memberships->isEmpty()) {
            return [
                'active_memberships' => [],
                'inactive_memberships' => [],
            ];
        }

        $modulesUtils = collect(MembershipHelper::getAllMembershipModules()['modules']);
        $subModules = $modulesUtils->pluck('sub_modules')->flatten(1);

        $memberships->each(function ($membership) use ($subModules, $modulesUtils) {
            $hasPayTransaction = isset($membership->payTransaction);
            $membership->users_quantity = $hasPayTransaction ? $membership->payTransaction->users_quantity : 0;
            $membership->pages_quantity = $hasPayTransaction ? $membership->payTransaction->pages_quantity : 0;
            unset($membership->payTransaction);

            $membership->modules->each(function ($module) use ($subModules, $modulesUtils, $membership) {
                $moduleUtilsTemp = $modulesUtils->firstWhere('id', $module->membership_modules_id);
                $this->mapModuleAttributes($module, $moduleUtilsTemp, $membership->purchase_date, $membership->initial_date);

                $module->membership_submodules = $module->membershipSubmodules->map(function ($submodule) use ($subModules) {
                    $submoduleUtilsTemp = $subModules->firstWhere('id', $submodule->sub_module_id);
                    return $this->mapSubmoduleAttributes($submodule, $submoduleUtilsTemp);
                });
            });
        });

        $membershipsFiltered = $memberships->reverse()->groupBy('is_active');

        $membershipsActiveFiltered = $membershipsFiltered[1] ?? [];
        $membershipsInactiveFiltered = $membershipsFiltered[0] ?? [];

        return [
            'active_memberships' => $membershipsActiveFiltered,
            'inactive_memberships' => $membershipsInactiveFiltered,
        ];
    }

    /**
     * map submodule attributes
     * @param Object $module module
     * @param Collection $moduleUtilsTemp collection
     * @param string $purchaseDate
     * @param string $initialDate
     */
    private function mapModuleAttributes($module, $moduleUtilsTemp, $purchaseDate, $initialDate)
    {
        $module->nameModule = $module->name;
        $module->priceYearOld = (float) $module->price;
        $module->netPrice = (float) $module->price_old;
        $module->name = $moduleUtilsTemp['name'] ?? '';
        $module->price_year = $moduleUtilsTemp['price_year'] ?? 0;
        $module->price_month = $moduleUtilsTemp['price_month'] ?? 0;
        $module->price_semester = $moduleUtilsTemp['price_semester'] ?? 0;
        $module->price_semester_month = $moduleUtilsTemp['price_semester_month'] ?? 0;
        $module->percentage_discount = $moduleUtilsTemp['discount'] ?? 0;
        $module->purchase_date = $purchaseDate;

        $initialDay = Carbon::parse($initialDate);
        $expirationDay = Carbon::parse($module->expiration_date);
        $module->is_annual = $initialDay->diffInMonthsInt($expirationDay) == 12;
        $module->initial_date = $initialDay->getTimestamp();
        $module->expiration_date = $expirationDay->getTimestamp();
    }

    /**
     * map submodule attributes
     * @param Object $submodule submodule
     * @param Collection $submoduleUtilsTemp collection
     * @return Object
     */
    private function mapSubmoduleAttributes($submodule, $submoduleUtilsTemp): object
    {
        $submodule->nameModule = $submodule->name;
        $submodule->priceYearOld = (float) $submodule->price;
        $submodule->netPrice = (float) $submodule->price_old;
        $submodule->name = $submoduleUtilsTemp['name'] ?? '';
        $submodule->price_year = $submoduleUtilsTemp['price_year'] ?? 0;
        $submodule->price_month = $submoduleUtilsTemp['price_month'] ?? 0;
        $submodule->price_semester = $submoduleUtilsTemp['price_semester'] ?? 0;
        $submodule->price_semester_month = $submoduleUtilsTemp['price_semester_month'] ?? 0;
        $submodule->price_maintenance_year = $submoduleUtilsTemp['price_maintenance_year'] ?? 0;
        $submodule->percentage_discount = $submoduleUtilsTemp['discount'] ?? 0;
        $submodule->quantity = $submoduleUtilsTemp['quantity'] ?? $submodule->total_invoices ?? 0;
        $submodule->discount = $submodule->discount;

        unset($submodule->membershipHasModule);
        return $submodule;
    }

    /**
     * set false attribute is_active Memberships
     * @param collection $memberships collect
     * @param boolean $isActive boolean
     * @return Collection
     */
    private function setFalseIsActiveMemberships($memberships, $isActive = false)
    {
        foreach ($memberships as $membership) {
            $membership->is_active = $isActive;
            foreach ($membership->modules as $module) {
                $module->is_active = $isActive;
                foreach ($module->membershipSubmodules as $submodule) {
                    $submodule->is_active = $isActive;
                }
            }
        }
        return $memberships;
    }

    /**
     * Create binnacle for membership by company_Id
     * @param string $company_id uuid
     * @return array
     * @throws GuzzleException
     */
    public function getBinnacleMembership(string $company_id): array
    {
        $this->dataBinnacleModules = collect([]);
        $paymentStatus = [
            $this->model::PAYMENT_STATUS_APPROVED => 'Aprobado',
            $this->model::PAYMENT_STATUS_PENDING => 'Pendiente',
            $this->model::PAYMENT_STATUS_DECLINED => 'Rechazado',
            $this->model::PAYMENT_STATUS_ERROR => 'Error',
        ];

        $memberships = $this->model::with([
            'payTransaction',
            'modules.membershipSubmodules',
            'modules' => function ($query) {
                $query->whereNotIn('membership_modules_id', collect($this->membershipHasModuleModel::FREE_MODULES)->pluck('id'));
            }
        ])
            ->where('company_id', $company_id)
            ->orderBy('purchase_date')
            ->get();


        if ($memberships->isEmpty()) {
            $company = $this->companyModel->find($company_id);
            return [
                'type' => 'xlsx',
                'module' => 'membership-binnacle',
                'start_date' => Carbon::make($company->created_at)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
                'data' => []
            ];
        }

        $moduleWebsite = $memberships->where('is_active', true)
            ->pluck('modules')
            ->flatten(1)
            ->firstWhere('membership_modules_id', $this->membershipHasModuleModel::MODULE_WEB_SITE);
        $moduleWebsite = isset($moduleWebsite) ? $moduleWebsite->membership : collect([]);

        $moduleInvoice = $memberships->where('is_active', true)
            ->pluck('modules')
            ->flatten(1)
            ->firstWhere('membership_modules_id', $this->membershipHasModuleModel::MODULE_INVOICE_ID);
        $moduleInvoice = isset($moduleInvoice) ? $moduleInvoice->membership : collect([]);

        $modulesUtils = collect(MembershipHelper::getAllMembershipModules()['modules']);
        $subModules = $modulesUtils->pluck('sub_modules')->flatten(1);

        $this->dataBinnacleModules[] = collect([
            'purchase_date' => Carbon::parse($memberships->first()->purchase_date)->format('Y-m-d H:i:s'),
            'module' => 'Usuarios adicionales',
            'details' => 'Obsequio primer compra',
            'quantity' => 3,
            'price' => 0,
            'has_discount' => false,
            'discount' => 0,
            'total' => 0,
            'renewal_date' => 'N/A',
            'payment_status' => 'N/A',
        ]);

        if ($moduleInvoice->count() > 0) {
            $this->dataBinnacleModules[] = collect([
                'purchase_date' => Carbon::parse($moduleInvoice->purchase_date)->format('Y-m-d H:i:s'),
                'module' => 'Facturación electrónica',
                'details' => 'Paquete 15 facturas - Obsequio primer compra',
                'quantity' => 1,
                'price' => 0,
                'has_discount' => false,
                'discount' => 0,
                'total' => 0,
                'renewal_date' => 'N/A',
                'payment_status' => 'N/A',
            ]);
        }

        $memberships->each(function ($membership) use ($subModules, $modulesUtils, $moduleWebsite, $paymentStatus) {
            $hasPayTransaction = isset($membership->payTransaction);
            $users_quantity = $hasPayTransaction ? $membership->payTransaction->users_quantity : 0;
            $pages_quantity = $hasPayTransaction ? $membership->payTransaction->pages_quantity : 0;

            $membershipPurchaseDate = Carbon::parse($membership->purchase_date)->format('Y-m-d H:i:s');
            $membershipExpirationDate = Carbon::parse($membership->expiration_date)->format('Y-m-d');

            if ($users_quantity > 0) {
                $this->dataBinnacleModules[] = collect([
                    'purchase_date' => $membershipPurchaseDate,
                    'module' => 'Usuarios adicionales',
                    'details' => 'Usuarios adicionales',
                    'quantity' => $users_quantity,
                    'price' => $this->model::PRICE_USER_MEMBERSHIP,
                    'has_discount' => false,
                    'discount' => 0,
                    'total' => $users_quantity * $this->model::PRICE_USER_MEMBERSHIP,
                    'renewal_date' => 'N/A',
                    'payment_status' => $paymentStatus[$membership->payment_status] ?? $paymentStatus[$this->model::PAYMENT_STATUS_ERROR],
                ]);
            }

            $validateIfExist = $membership->modules->firstWhere('membership_modules_id', $this->membershipHasModuleModel::MODULE_WEB_SITE);
            if ($pages_quantity > 0 && isset($validateIfExist)) {
                $price = $this->membershipHasModuleModel::PRICE_BASE_ADITIONAL_PAGE * $pages_quantity;
                $priceDiscountBase = ($this->membershipHasModuleModel::PRICE_DISCOUNT_ADITIONAL_PAGE * $pages_quantity);
                $percentageDiscount = $priceDiscountBase * ($moduleWebsite->count() > 0 ? $moduleWebsite->discount : $validateIfExist->discount) / 100;
                $discount = ($price - $priceDiscountBase - $percentageDiscount) * $pages_quantity;
                $this->dataBinnacleModules[] = collect([
                    'purchase_date' => $membershipPurchaseDate,
                    'module' => 'Servicios de sitio web y tienda virtual',
                    'details' => 'Páginas adicionales',
                    'quantity' => $pages_quantity,
                    'price' => $price,
                    'has_discount' => true,
                    'discount' => $discount,
                    'total' => $price - $discount,
                    'renewal_date' => ($moduleWebsite->count() > 0 ? Carbon::parse($moduleWebsite->expiration_date)->format('Y-m-d') : Carbon::parse($validateIfExist->expiration_date)->format('Y-m-d')),
                    'payment_status' => $paymentStatus[$membership->payment_status] ?? $paymentStatus[$this->model::PAYMENT_STATUS_ERROR],
                ]);
            }

            $membership->modules->each(function ($module) use ($subModules, $modulesUtils, $membership, $paymentStatus, $membershipPurchaseDate, $membershipExpirationDate) {
                $moduleUtilsTemp = $modulesUtils->firstWhere('id', $module->membership_modules_id);

                if ($module->membership_modules_id != $this->membershipHasModuleModel::MODULE_INVOICE_ID && $module->membership_modules_id != $this->membershipHasModuleModel::MODULE_WEB_SITE) {
                    $this->dataBinnacleModules[] = collect([
                        'purchase_date' => $membershipPurchaseDate,
                        'module' => $moduleUtilsTemp['name'],
                        'details' => 'Completo',
                        'quantity' => 1,
                        'price' => $moduleUtilsTemp['price_year'],
                        'has_discount' => $module->percentage_discount != 0,
                        'discount' => $moduleUtilsTemp['price_year'] * ($module->percentage_discount / 100),
                        'total' => $moduleUtilsTemp['price_year'] * (1 - $module->percentage_discount / 100),
                        'renewal_date' => $membershipExpirationDate,
                        'payment_status' => $paymentStatus[$membership->payment_status] ?? $paymentStatus[$this->model::PAYMENT_STATUS_ERROR],
                    ]);
                } else {
                    $module->membershipSubmodules->each(function ($submodule) use ($subModules, $module, $moduleUtilsTemp, $membership, $paymentStatus, $membershipPurchaseDate, $membershipExpirationDate) {
                        $submoduleUtilsTemp = $subModules->firstWhere('id', $submodule->sub_module_id);

                        $price = ($submoduleUtilsTemp['price_year'] ?? 0) + ($submoduleUtilsTemp['price_maintenance_year'] ?? 0);
                        $type = ($module->membership_modules_id == $this->membershipHasModuleModel::MODULE_INVOICE_ID) ? 'Paquete ' : '';
                        $this->dataBinnacleModules[] = collect([
                            'purchase_date' => $membershipPurchaseDate,
                            'module' => $moduleUtilsTemp['name'] ?? '',
                            'details' => $type . ($submoduleUtilsTemp['name'] ?? ''),
                            'quantity' => 1,
                            'price' => $price ?? 0,
                            'has_discount' => $module->percentage_discount != 0,
                            'discount' => $price * ($module->percentage_discount / 100),
                            'total' => $price * (1 - $module->percentage_discount / 100),
                            'renewal_date' => $membershipExpirationDate,
                            'payment_status' => $paymentStatus[$membership->payment_status] ?? $paymentStatus[$this->model::PAYMENT_STATUS_ERROR],
                        ]);
                    });
                }
            });
        });

        $company = $this->companyModel->find($company_id);
        $data = $this->dataBinnacleModules->sortByDesc('purchase_date')->values()->all();

        return [
            'type' => 'xlsx',
            'module' => 'membership-binnacle',
            'start_date' => Carbon::make($company->created_at)->format('Y-m-d'),
            'end_date' => Carbon::now()->format('Y-m-d'),
            'data' => $data
        ];
    }

    /**
     * When the membership has no modules with recurrent payment, it cancels the membership recurrent payment.
     * @param Membership $membership
     * @return array
     */
    public function validateAccessFreeDocuments(string $company_id, array $request)
    {
        $now = Carbon::now();
        $allModulesUtils = MembershipHelper::getAllMembershipModules()['modules'];

        $documents = DB::table('memberships as m')
            ->join('membership_has_modules as mm', 'm.id', '=', 'mm.membership_id')
            ->join('membership_submodules as mms', 'mm.id', '=', 'mms.membership_has_modules_id')
            ->where('m.company_id', '=', $company_id)
            ->where('mms.expiration_date', '>=', $now)
            ->where('mms.total_invoices', 15)
            ->where('mms.sub_module_id', 1)->get();

        if ($documents->isNotEmpty()) {
            if ($documents->first()->remaining_invoices == 0) {
                return "15 free documents used, year not over yet";
            }
            return "You already have an active 15 document plan";
        }

        if ($request["number_employees"] > config('app.validation_documents.number_employees') || $request["total_revenue"] > config('app.validation_documents.income_microenterprise')) {
            return "You do not meet the requirements for the plan";
        }

        if (!$request["is_validation"]) {
            $membership = $this->model::create([
                'purchase_date' => Carbon::now(),
                'initial_date' => Carbon::now(),
                'is_active' => true,
                'is_frequent_payment' => false,
                'company_id' => $company_id,
                'price' => 0,
                'is_first_payment' => false,
                "payment_status" => $this->model::PAYMENT_STATUS_APPROVED,
                'expiration_date' => Carbon::now()->addMonths($this->model::EXPIRATION_DATE_MEMBERSHIP),
                'payment_method' => $this->model::PAYMENT_METHOD_PAYU
            ]);

            $planName = $this->membershipHasModuleModel::NAME_INVOICE_PLAN . '15 documentos';

            $module = MembershipHasModules::create([
                'membership_id' => $membership->id,
                'membership_modules_id' => 3,
                'is_active' => true,
                'percentage_discount' => 0,
                'expiration_date' => Carbon::now()->addMonths($this->model::EXPIRATION_DATE_MEMBERSHIP),
                'name' => $planName

            ]);

            MembershipSubModule::create([
                'membership_has_modules_id' => $module->id,
                'sub_module_id' => 1,
                'is_active' => true,
                'total_invoices' => 15,
                'remaining_invoices' => 15,
                'expiration_date' => Carbon::now()->addMonths($this->model::EXPIRATION_DATE_MEMBERSHIP),
                'name' => $planName

            ]);

            $this->membershipDetailEloquent->storeMembershipHasModules(
                $this->membershipHasModuleModel::FREE_MODULES,
                $membership,
                $allModulesUtils
            );
            $this->membershipDetailEloquent->activeModulesByMembershipId($membership);

            return "A 15 document plan has been created";
        }

        return "It is valid for the 15 document plan";
    }

    /**
     * Returns all purchased membership plans from all companies for the administrator user
     * @return array
     */
    public function getMembershipsAllCompaniesAdministration()
    {
        return $this->companyModel::with([
            'memberships' => function ($query) {
                $query->with([
                    'modules' => function ($query) {
                        return $query->select('id', 'membership_id', 'membership_modules_id', 'is_active', 'expiration_date', 'name')
                            ->whereNotNull('expiration_date')
                            ->whereIn('membership_modules_id', MembershipHasModules::PURCHASABLE_MODULES);
                    },
                    'modules.membershipSubmodules'
                ])->select('id', 'initial_date', 'company_id', 'expiration_date')
                    ->where('payment_method', '!=', null)
                    ->where('payment_status', $this->model::PAYMENT_STATUS_APPROVED)
                    ->whereHas('modules', function ($query) {
                        return $query->whereNotNull('expiration_date')
                            ->whereIn('membership_modules_id', MembershipHasModules::PURCHASABLE_MODULES);
                    });
            }
        ])->whereHas('memberships')->whereHas('memberships.modules')->where('is_test_account', false)->get();
    }
}
