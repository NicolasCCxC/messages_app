<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_api_keys', function (Blueprint $table) {
            $table->uuid('companies_id');
            $table->uuid('api_keys_id');
            $table->timestamps();

            $table->foreign('companies_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('api_keys_id')->references('id')->on('api_keys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies_api_keys');
    }
}
