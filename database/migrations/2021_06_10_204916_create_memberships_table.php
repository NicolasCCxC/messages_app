<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->string('purchase_date')->nullable()->comment('date on which the purchase was made');
            $table->double('price');
            $table->boolean('is_active')->nullable()->comment('this is to know if the membership is active');
            $table->boolean('is_first_payment')->default(true);
            $table->boolean('is_frequent_payment')->default(false);
            $table->string('initial_date')->nullable()->comment('when the membership start');
            $table->string('expiration_date')->nullable()->comment('when the membership end');
            $table->uuid('transaction_id')->nullable()->comment('Payu number of transaction');
            $table->bigInteger('membership_type_id')->unsigned();
            $table->foreign('membership_type_id')->references('id')->on('membership_types')->cascadeOnDelete();
            $table->string('invoice_id')->nullable()->comment('id invoice');
            $table->string('invoice_pdf')->nullable()->comment('url invoice pdf membership');
            $table->boolean('email_send')->nullable()->default(false)->comment('when the email sending to client (company)');

            $table->index('membership_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memberships');
    }
}
