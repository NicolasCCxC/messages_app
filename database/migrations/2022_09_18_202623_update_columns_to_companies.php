<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('person_type')->nullable()->change();
            $table->string('company_representative_name')->nullable()->change();
            $table->bigInteger('phone')->nullable()->change();
            $table->integer('country_id')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->date('company_privacy_acceptation_date')->nullable()->change();
            $table->bigInteger('invoices_available')->default(15);
            $table->bigInteger('users_available')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('person_type')->nullable(false)->change();
            $table->string('company_representative_name')->nullable(false)->change();
            $table->bigInteger('phone')->nullable(false)->change();
            $table->integer('country_id')->nullable(false)->change();
            $table->string('postal_code')->nullable(false)->change();
            $table->date('company_privacy_acceptation_date')->nullable(false)->change();
            $table->removeColumn('invoices_available');
            $table->removeColumn('users_available');
        });
    }
}
