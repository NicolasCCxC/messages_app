<?php

namespace App\Console\Commands;

use App\Infrastructure\Formulation\MembershipHelper;
use App\Infrastructure\Persistence\MembershipEloquent;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Models\PayTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecurrentPaymentMemberships extends Command
{
    private $membershipModel;
    private $payTransactionModel;
    private $companyModel;
    private $membershipHasModulesModel;
    private $membershipSubModulesModel;
    private $membershipEloquent;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay:recurrent-payment-membership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recurrent payment for memberships';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        Membership     $membershipModel,
        MembershipHasModules $membershipHasModulesModel,
        MembershipSubModule $membershipSubModulesModel,
        PayTransaction $payTransactionModel,
        Company        $companyModel,
        MembershipEloquent $membershipEloquent
    )
    {
        parent::__construct();
        $this->membershipModel = $membershipModel;
        $this->payTransactionModel = $payTransactionModel;
        $this->companyModel = $companyModel;
        $this->membershipHasModulesModel = $membershipHasModulesModel;
        $this->membershipSubModulesModel = $membershipSubModulesModel;
        $this->membershipEloquent = $membershipEloquent;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $membershipsByCompany = $this->membershipModel::with(['modules' => function($query) use ($today){
            return $query->where('expiration_date', '<=' , $today)->where('is_active', true);
        },'payTransaction'])->where('payment_method', '!=', null)
            ->where('is_active', true)
            ->where('payment_status', $this->membershipModel::PAYMENT_STATUS_APPROVED)
            ->whereHas('modules', function($query) use ($today){
                return $query->where('expiration_date', '<=' , $today)->where('is_active', true);
            })->get()->groupBy('company_id');

        $membershipsByCompany->each(function ($memberships) {
            $memberships->each(function($membership){
                $membership->modules->each(function($module){
                    $module->update(['is_active' => false]);
                    $module->first()->membershipSubmodules()->update(['is_active' => false]);
                });

                $membershipModulesActive = $this->membershipModel::where('id', $membership->id)->whereHas('modules', function($query) {
                    return $query->where('is_active', true)->whereIn('membership_modules_id', MembershipHasModules::PURCHASABLE_MODULES);
                })->get();

                if($membershipModulesActive->isEmpty()){
                    $membership->update(['is_active' => false]);
                }

                $jsonData = collect($membership)["pay_transaction"] ? json_decode(collect($membership)["pay_transaction"]["json_invoice"], true) : null;
                $paymentMethod = isset($jsonData["paymentMethod"]) ? $jsonData["paymentMethod"] : null;

                if($paymentMethod && $paymentMethod != PayTransaction::PSE) {
                    $customerData =  $jsonData["additional_customer_data"];
                    $modules = $membership->modules->map(function($module) use($membership){
                        if($module->is_frequent_payment){
                            $module->load('membershipSubmodules');
                            $initialDay = Carbon::parse($membership->initial_date);
                            $expirationDay = Carbon::parse($module->expiration_date);
                            $diffMonths = Carbon::parse($initialDay)->diffInMonthsInt($expirationDay);
                            $subModules = $module->membershipSubmodules->map(function($subModule) use($diffMonths){
                                $subModule->is_active = false;
                                $subModule->save();
                                return ['id' => $subModule->sub_module_id, 'expiration_date' => $diffMonths];
                            });
                            $moduleData = ['id' => $module->membership_modules_id, 'sub_modules' => $subModules->toArray()];
                            if(count($subModules)<=0){
                                $moduleData['expiration_date'] = $diffMonths;
                            }
                            $module->is_active = false;
                            $module->save();
                            return $moduleData;
                        }
                    });
                    if($membership->is_frequent_payment) {
                        $data = [
                            "is_immediate_purchase" => false,
                            "company_id" => $membership->company_id,
                            "users_quantity" => 0,
                            "pages_quantity" => 0,
                            "is_frequent_payment" => true,
                            "modules" => $modules->toArray(),
                            'additional_customer_data' =>  $customerData
                        ];
                        $data['option_pay'] = 'PAY_WITH_TOKEN';
                        $this->membershipEloquent->storeMembership($data);
                    }
                }
            });
        });
    }
}
