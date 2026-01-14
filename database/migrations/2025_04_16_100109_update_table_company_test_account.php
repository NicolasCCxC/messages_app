<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class UpdateTableCompanyTestAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!config('app.debug')){
            Company::where('id', "4e11c9ef-f7ea-4e68-b32a-8d2f7a9fe096")
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
            Company::where('id', "4e11c9ef-f7ea-4e68-b32a-8d2f7a9fe096")
            ->update([
                'is_test_account' => true
            ]);
        }
    }
}
