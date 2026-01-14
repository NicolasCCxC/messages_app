<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use Illuminate\Support\Str;

class UpdateOldMembershipExpired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $initialDate = Carbon::now()->subMonths(6)->format('Y-m-d');
        $membershipsByCompany = Membership::with(['modules'])->where('initial_date', '<=' , $initialDate)->get()->groupBy('company_id');
        $membershipsByCompany->each(function ($memberships) {
            $memberships->each(function($membership){
                $membership->modules->each(function($module) {
                    $today = Carbon::now()->format('Y-m-d');
                    if($module->expiration_date <= $today) {
                        $module->update(['is_active' => false]);
                        $module->membershipSubmodules()->update(['is_active' => false]);
                    }
                });
                $countModules = $membership->modules->count();
                $countModulesInactive = $membership->modules->where('is_active', false)->count();
                if($countModules == $countModulesInactive){
                    $membership->update(['is_active' => false]);
                }
            });
        });

        $membershipCCXC = Membership::with(['modules'])->where('company_id', '83e80ae5-affc-32b4-b11d-b4cab371c48b')->get();        
        $membershipCCXC->each(function($membership){
            $membership->update(['is_active' => false]);
            $membership->modules->each(function($module) {
                $module->update(['is_active' => false]);
                $module->first()->membershipSubmodules()->update(['is_active' => false]);
            });
            }
        );

        $expirationDate = Carbon::now()->addMonths(12)->format('Y-m-d');
        $membershipDate = Carbon::now()->format('Y-m-d');

        $membership = Membership::create([
            "id" => Str::uuid()->toString(), 
            "company_id" => "83e80ae5-affc-32b4-b11d-b4cab371c48b", 
            "purchase_date" => 1676500791, 
            "price" => 972273.80, 
            "is_active" => true, 
            "is_first_payment" => false, 
            "is_frequent_payment" => false, 
            "initial_date" => $membershipDate, 
            "expiration_date" => $expirationDate, 
            "transaction_id" => null, 
            "invoice_id" => "7d124a37-b561-4a21-a3f9-6d2b1e43fc7a", 
            "invoice_pdf" => "https://storageccxc1.s3.us-west-2.amazonaws.com/famiefi/83e80ae5-affc-32b4-b11d-b4cab371c48b/bucket/electronic-document/2023-02-15-83e80ae5-affc-32b4-b11d-b4cab371c48b-1676500799.pdf", 
            "email_send" => true, 
            "payment_status" => "APPROVED", 
            "payment_method" => "PAYU", 
            "invoice_credit_note_id" => null, 
            "invoice_credit_note_pdf" => null 
         ]);

         $invoiceId =  Str::uuid()->toString();
         $webDesign = Str::uuid()->toString();

         $modules = [
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 14,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 12,
                "name" => "Documento soporte",
                "price_old" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 4,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 251988.00,
                "months" => 12,
                "name" => "Administración de bodegas",
                "price_old" => 359982.86
            ],
            [
                "id" => $webDesign,
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 2,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ],
            [
                "id" => $invoiceId,
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 3,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 6,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 10,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 11,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 12,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_id" => $membership->id,
                "is_active" => true,
                "membership_modules_id" => 13,
                "percentage_discount" => 0,
                "is_frequent_payment" => false,
                "expiration_date" => "2024-10-27",
                "price" => 0,
                "months" => 0,
                "name" => "",
                "price_old" => 0
            ]
        ];

        MembershipHasModules::insert($modules);

        $subModuleWebsite = [
            [
                "id" => Str::uuid()->toString(),
                "membership_has_modules_id" => $invoiceId,
                "sub_module_id" => 11,
                "is_active" => true,
                "created_at" => $membershipDate,
                "updated_at" => $membershipDate,
                "is_frequent_payment" => false,
                "total_invoices" => 0,
                "remaining_invoices" => 0,
                "expiration_date" =>   $expirationDate,
                "price" => 535499,
                "months" => 12,
                "name" => "Facturación electrónica - Documento Ilimitados",
                "price_old" => 535499,
                "discount" => 0
            ],
            [
                "id" => Str::uuid()->toString(),
                "membership_has_modules_id" => $webDesign,
                "sub_module_id" => 10,
                "is_active" => true,
                "created_at" => $membershipDate,
                "updated_at" => $membershipDate,
                "is_frequent_payment" => false,
                "total_invoices" => 0,
                "remaining_invoices" => 0,
                "expiration_date" =>   $expirationDate,
                "price" => 251988.00,
                "months" => 12,
                "name" => "Sitio web y tienda virtual: Plan Premium",
                "price_old" => 359982.86,
                "discount" => 107994.86
            ]
        ];

        MembershipSubModule::insert($subModuleWebsite);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
