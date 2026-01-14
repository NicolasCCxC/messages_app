<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancelModulesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_modules_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('membership_has_modules_id')->references('id')->on('membership_has_modules')->cascadeOnDelete();
            $table->string('reason');
            $table->foreignUuid('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignUuid('membership_id')->references('id')->on('memberships')->cascadeOnDelete();
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
        Schema::dropIfExists('cancel_modules_details');
    }
}
