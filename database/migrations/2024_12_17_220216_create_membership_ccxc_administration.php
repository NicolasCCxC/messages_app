<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use Carbon\Carbon;

class CreateMembershipCcxcAdministration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $membership = Membership::create([
            'purchase_date' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'initial_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_active' => true,
            'is_frequent_payment' => false,
            'company_id' => Company::COMPANY_CCXC,
            'price' => 0,
            'is_first_payment' => false,
            'expiration_date' => Carbon::now()->addYears(10),
            'payment_method' => Membership::PAYMENT_METHOD_FREE,
            'payment_status' => 'APPROVED'
        ]);

        MembershipHasModules::create([
            'membership_id' => $membership->id,
            'membership_modules_id' => 15,
            'is_active' => true,
            'percentage_discount' => 0,
            'expiration_date' => Carbon::now()->addYears(10)->toDateString(),
            'price' => 0,
            'price_old' => 0,
            'months' => 12,
            'name' => 'Clientes diggi pymes',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $membershipId = DB::table('membership_has_modules')
            ->where('name', 'Clientes diggi pymes')
            ->where('membership_modules_id', 15)
            ->value('membership_id');
        
        if ($membershipId) {
            DB::table('membership_has_modules')
            ->where('membership_id', $membershipId)
            ->where('name', 'Clientes diggi pymes')
            ->where('membership_modules_id', 15)
            ->delete();

            DB::table('memberships')
            ->where('id', $membershipId)
            ->delete();
        }
    }
}
