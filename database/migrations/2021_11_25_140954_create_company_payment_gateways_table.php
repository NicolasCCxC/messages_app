<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyPaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_payment_gateways', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('payment_gateway_id');
            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->cascadeOnDelete();
            $table->jsonb('credentials')->unique();
            $table->string('date');
            $table->uuid('company_information_id');
            $table->foreign('company_information_id')->references('id')->on('company_information')->cascadeOnDelete();
            $table->softDeletes();

            $table->unique(['company_information_id','payment_gateway_id']);
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
        Schema::dropIfExists('company_payment_gateways');
    }
}
