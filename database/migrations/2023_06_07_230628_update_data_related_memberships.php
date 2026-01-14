<?php

use App\Models\Company;
use App\Models\MembershipSubModule;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataRelatedMemberships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $company = Company::find('83e80ae5-affc-32b4-b11d-b4cab371c48b');
        $company->created_at = $company->updated_at = Carbon::create('2022', '9', '20');
        $company->save();

        MembershipSubModule::where('sub_module_id', '>', 6)->update(['sub_module_id' => 6]);
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
