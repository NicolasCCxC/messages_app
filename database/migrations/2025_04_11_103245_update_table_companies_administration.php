<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class UpdateTableCompaniesAdministration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!config('app.debug')){
            Company::where('id', "cd16ca60-54f5-4de5-ae4b-e7928303b084")
            ->update([
                'is_test_account' => false
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(!config('app.debug')){
            Company::where('id', "cd16ca60-54f5-4de5-ae4b-e7928303b084")
            ->update([
                'is_test_account' => true
            ]);
        }
    }
}
