<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipPurchaseProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_purchase_process', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reference_id')->nullable();
            $table->foreignUuid('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->double('price');
            $table->boolean('is_payment')->default(false);
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
        Schema::dropIfExists('membership_purchase_process');
    }
}
