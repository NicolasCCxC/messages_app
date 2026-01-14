<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrefixesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prefixes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('resolution_number')->nullable();
            $table->string('resolution_technical_key')->nullable();
            $table->string('type');
            $table->string('prefix');
            $table->date('initial_validity');
            $table->date('final_validity');
            $table->integer('final_authorization_range');
            $table->integer('initial_authorization_range');
            $table->boolean('physical_store')->default(false);
            $table->boolean('website')->default(false);
            $table->boolean('contingency')->default(false);
            $table->uuid('company_id');
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
        Schema::dropIfExists('prefixes');
    }
}
