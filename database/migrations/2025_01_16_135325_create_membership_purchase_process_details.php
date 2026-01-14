<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipPurchaseProcessDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_purchase_process_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_process_id');
            $table->bigInteger('module_id');
            $table->bigInteger('sub_module_id')->nullable();
            $table->foreign('purchase_process_id')->references('id')->on('membership_purchase_process');
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
        Schema::dropIfExists('membership_purchase_process_details');
    }
}
