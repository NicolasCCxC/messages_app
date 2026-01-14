<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->index();
            $table->string('date_approved')->nullable();
            $table->string('status')->default('');
            $table->string('date_payment')->nullable();
            $table->uuid('client_id')->index('idx_client_id_payments');
            $table->string('amount');
            $table->string('payment_number')->nullable();
            $table->string('url_pdf')->nullable();
            $table->string('url_html')->nullable();
            $table->uuid('purchase_order_id')->index();
            $table->uuid('payment_method_id')->index();
            $table->foreignUuid('company_information_id')->references('id')->on('company_information')->cascadeOnDelete();
            $table->foreignUuid('company_payment_gateway_id')->references('id')->on('company_payment_gateways')->cascadeOnDelete();
            $table->softDeletes();
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
        Schema::dropIfExists('payments');
    }
}
