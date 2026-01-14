<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCiiusCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ciius_company', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->index('idx_ciius_on_ciius');
            $table->string('name');
            $table->unsignedInteger('ciiu_id');
            $table->uuid('company_id');
            $table->boolean('is_main')->default(false);
            $table->timestamps();

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
        Schema::dropIfExists('ciius_company');
    }
}
