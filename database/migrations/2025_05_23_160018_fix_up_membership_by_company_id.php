<?php

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUpMembershipByCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $membership = Membership::with([
            'modules' => function ($query) {
                $query->where('membership_modules_id', '!=', 2);
            },
            'modules.membershipSubmodules'
        ])->find('96a7e54e-f2fe-4ef6-9419-fd926b51c89c');


        if ($membership != null) {
            $expiration_date = Carbon::now()->addYear()->toDateString();
            $membership->modules->each(function ($module) use ($expiration_date) {
                $module->update(['is_active' => true, 'expiration_date' => $expiration_date]);

                $module->membershipSubmodules->each(function ($submodule) use ($expiration_date) {
                    $submodule->update(['is_active' => true, 'expiration_date' => $expiration_date]);
                });
            });
        }
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
