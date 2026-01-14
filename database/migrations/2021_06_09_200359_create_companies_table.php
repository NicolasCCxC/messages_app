<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('person_type');
            $table->uuid('document_type');
            $table->string('document_number')->unique();
            $table->string('company_representative_name');
            $table->uuid('foreign_exchange_id')->nullable();
            $table->string('foreign_exchange_code')->nullable();
            $table->bigInteger('phone');
            $table->integer('country_id');
            $table->string('country_name')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('department_name')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city_name')->nullable();
            $table->string('postal_code');
            $table->string('address')->nullable();
            $table->string('domain')->nullable();
            $table->string('make_web_page_type')->nullable();
            $table->boolean('brand_established_service');
            $table->boolean('accept_company_privacy');
            $table->boolean('has_a_physical_store')->default(false);
            $table->boolean('has_e_commerce')->default(false);
            $table->date('company_privacy_acceptation_date');
            $table->bigInteger('whatsapp')->nullable();
            $table->string('tax_detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
