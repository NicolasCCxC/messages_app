<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Membership;
use App\Models\Company;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Models\PayTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Traits\CommunicationBetweenServicesTrait;
use App\Infrastructure\Services\NotificationsService;
use App\Infrastructure\Persistence\MembershipEloquent;
use App\Infrastructure\Persistence\MembershipDetailEloquent;
use App\Infrastructure\Persistence\UserEloquent;
use App\Infrastructure\Services\PayService;
use App\Infrastructure\Formulation\MembershipHelper;
use App\Enums\Notification as NotificationEnum;
use App\Infrastructure\Persistence\MembershipPurchaseProcessEloquent;
use App\Helpers\MembershipCalculateHelper;
use App\Enums\PayTransactionEnum;


class ValidatePayTransaction extends Command
{
    use CommunicationBetweenServicesTrait;

    /**
     * @var MembershipEloquent
     */
    private $membershipEloquent;

    /**
     * @var UserEloquent
     */
    private $userEloquent;

    /**
     * @var Company
     */
    private $companyModel;

    /**
     * @var MembershipPurchaseProcessEloquent
    */
    private $membershipPurchaseProcessEloquent;

    /**
     * @var MembershipDetailEloquent
    */
    private $membershipDetailEloquent;

    /**
     * @var NotificationsService
     */
    private $notificationsService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:pay-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate pending transactions to active users or memberships';

    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->membershipEloquent = new MembershipEloquent();
        $this->userEloquent = new UserEloquent();
        $this->companyModel = new Company();
        $this->notificationsService = new NotificationsService();
        $this->membershipPurchaseProcessEloquent = new MembershipPurchaseProcessEloquent();
        $this->membershipDetailEloquent = new MembershipDetailEloquent();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payService = new PayService();
        $transactionsPending = PayTransaction::where('status', 'PENDING')->get();
        foreach ($transactionsPending as $transaction) {
            $data = ['transactionId' => $transaction->transaction_id];
            $response = $payService->getDetailTransaction($data)['result'];
            if (is_null($response)) {
                continue;
            } else {
                $response = $response['payload'];
            }
            if ($response['state'] === 'APPROVED') {
                $modulesNames = [];
                $currentModuleWebsite = Membership::where('company_id', $transaction->company_id)
                                    ->where('is_active', true)
                                    ->whereHas('modules', function ($query) {
                                        $query->where('membership_modules_id', MembershipHasModules::MODULE_WEB_SITE)
                                            ->where('is_active', true)
                                            ->whereHas('membershipSubmodules', function ($query) {
                                                $query->where('is_active', true);
                                            });
                                    })
                                    ->with(['modules' => function ($query) {
                                        $query->where('membership_modules_id', MembershipHasModules::MODULE_WEB_SITE)
                                            ->where('is_active', true)
                                            ->with('membershipSubmodules');
                                    }])
                                    ->first();

                if(isset($currentModuleWebsite)){
                    self::deactivateLastWebsite($currentModuleWebsite);
                }

                if ($transaction->membership_id != null) {
                    $membership = Membership::find($transaction->membership_id);
                    $this->membershipPurchaseProcessEloquent->updatePurchaseProcessPayment($membership->id, $transaction->company_id);
                    $membership->is_active = true;
                    $membership->payment_status = $response['state'];
                    $membership->payment_method = Membership::PAYMENT_METHOD_PAYU;
                    $membership->expiration_date = Carbon::parse($membership->initial_date)->addMonths(Membership::EXPIRATION_DATE_MEMBERSHIP);
                    $membership->save();
                    $transaction['json_invoice'] = json_decode($transaction['json_invoice'], true);
                    $invoice = $transaction['json_invoice'];
                    $membershipHasModules = MembershipHasModules::where('membership_id', $membership->id)->get();
                    foreach ($membershipHasModules as $membershipHasModule) {
                        if($membershipHasModule->membership_modules_id == MembershipHasModules::MODULE_WAREHOUSES){
                            array_push($modulesNames, [
                                "name" => $membershipHasModule->name
                            ]);
                        }
                        $membershipHasModule->is_active = true;
                        $membershipHasModule->save();

                        $membershipSubModules = MembershipSubModule::where('membership_has_modules_id', $membershipHasModule->id)->get();
                        foreach ($membershipSubModules as $membershipSubModule) {
                            if (in_array($membershipHasModule->membership_modules_id,[MembershipHasModules::MODULE_INVOICE_ID, MembershipHasModules::MODULE_WEB_SITE])){
                                array_push($modulesNames,
                                 [
                                     "name" => $membershipSubModule->name
                                 ]
                                );
                            }
                            $membershipSubModule->is_active = true;
                            $membershipSubModule->save();

                        }
                    }
                }
                if ($transaction->users_quantity > 0) {
                    $this->membershipEloquent->addUsersMembership($transaction->company_id, $transaction->users_quantity);
                }
                $membershipsActives = $this->membershipEloquent->getDetailsMembership($transaction->company_id);
                $allModulesUtils = MembershipHelper::getAllMembershipModules()['modules'];
                if (MembershipCalculateHelper::countValidModules($membershipsActives["active_memberships"]) > 1) {
                    $this->membershipDetailEloquent->createFreeMembershipModule(MembershipHasModules::PLANNING_ORGANIZATION, $membership, $allModulesUtils);
                }
                try {
                    $company = $this->companyModel::find($membership->company_id);
                    $user = $this->userEloquent->getUserByCompanyId($membership->company_id, $company->document_number);
                    $documents = MembershipHelper::getAllDocumentTypes();
                    $document = isset($company->document_type) ? collect($documents)->where('id',$company->document_type)->first() : null;

                    $data = [
                        'email_client' => isset($user->email) ? $user->email : null,
                        'document_number' => $company->document_number,
                        'document_type' => $document ? $document["name"] : null,
                        'purchase_date' => Carbon::parse($membership->purchase_date)->format('d/m/Y H:i:s'),
                        'module' => $modulesNames,
                        "total" => $membership->price,
                        'company_id' => $membership->company_id,
                        'notification' => [
                            'type' => NotificationEnum::NOTIFICATION_TYPE,
                            'module_notification_id' => NotificationEnum::MODULE_PAYMENT_PLANS,
                            'date' => Carbon::now()->format('Y-m-d'),
                            'consecutive' => 'Compra de membresía',
                            'reference' => $this->companyModel::COMPANY_CCXC,
                            'user_id' => $this->companyModel::COMPANY_CCXC,
                            'company_id' => $this->companyModel::COMPANY_CCXC,
                            'type_notification_id' => NotificationEnum::NOTIFICATION_TYPE_MEMBERSHIP_PURCHASE,
                            'state_notification_id' => NotificationEnum::STATE_NOTIFICATION_SEND,
                            'description' => "Email del Cliente: " . (isset($user->email) ? $user->email : 'No disponible') . " | " .
                                             "Número de Documento: " . $company->document_number . " | " .
                                             "Tipo de Documento: " . ($document ? $document["name"] : 'No disponible') . " | " .
                                             "Fecha de la Compra: " . Carbon::parse($membership->purchase_date)->format('d/m/Y H:i:s') . " | " .
                                             "Artículos Comprados: " . implode(", ", array_column($modulesNames, 'name')) . " | " .
                                             "Total: $" . number_format($membership->price, 2, ',', '.'),
                        ]
                    ];

                    $this->notificationsService->sendEmailSaleMembership($data);
                } catch (\Exception $e) {
                    Log::error('Error on ValidatePayTransaction', [
                        'transaction_id' => $transaction->id,
                        'mensaje' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
           
            }
            $transaction->status = $response['state'];

            if (!empty($transaction->json_pse_url_response)) {
                $transaction->json_pse_url_response =
                    $this->updatePseResponseUrl(
                        $transaction->json_pse_url_response,
                        $response['state']
                    );
            }
            $transaction->save();
        }
    }

    private function updatePseResponseUrl(?string $json, string $status): string
    {
        $data = [];

        if (!empty($json)) {
            $data = json_decode($json, true) ?? [];
        }

        $status = strtoupper($status);

        $data['message'] = PayTransactionEnum::STATUS_MESSAGES[$status] ?? PayTransactionEnum::STATUS_MESSAGES[PayTransactionEnum::STATUS_PENDING];
        $data['polResponseCode'] = (string)(PayTransactionEnum::STATUS_CODES[$status] ?? PayTransactionEnum::STATUS_CODES[PayTransactionEnum::STATUS_PENDING]);

        return json_encode($data);
    }
    
    public function deactivateLastWebsite($currentModuleWebsite): void
    {
        $countFreeModules = collect(MembershipHasModules::FREE_MODULES)->count();
        $actualCountModules = $currentModuleWebsite->modules()->count();
        $diffModules = $actualCountModules - $countFreeModules;
        if($diffModules > 1){
            $currentModuleWebsite->modules->first()->update(['is_active' => false]);
            $currentModuleWebsite->modules->first()->membershipSubmodules()->update(['is_active' => false]);
        }else{
            $currentModuleWebsite->update(['is_active' => false]);
            $currentModuleWebsite->modules()->update(['is_active' => false]);
            $currentModuleWebsite->modules->first()->membershipSubmodules()->update(['is_active' => false]);
        }
    }

}
