<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyPrivacyPurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_privacy_purposes', function (Blueprint $table) {
            $table->uuid('privacy_purpose_id');
            $table->uuid('company_id');
            $table->timestamps();

            $table->foreign('privacy_purpose_id')->references('id')->on('privacy_purposes')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_privacy_purposes');
    }
}
