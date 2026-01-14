<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurityFiscalResponsibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('security_fiscal_responsibilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_fiscal_responsibility');
            $table->string('number_resolution')->nullable();
            $table->date('date')->nullable();
            $table->foreignUuid('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('security_fiscal_responsibilities');
    }
}
