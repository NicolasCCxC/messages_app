<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_first_login')->default(true)->after('accept_data_policy');
            $table->boolean('accept_terms_conditions')->default(true)->after('accept_data_policy');
            $table->timestamp('user_terms_conditions_acceptation_date')->nullable()->after('user_privacy_acceptation_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->removeColumn('is_first_login');
            $table->removeColumn('accept_terms_conditions')('accept_data_policy');
            $table->removeColumn('user_terms_conditions_acceptation_date');
        });
    }
}
