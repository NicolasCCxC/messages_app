<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipSubmodulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_submodules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('membership_has_modules_id')->references('id')->on('membership_has_modules')->cascadeOnDelete();
            $table->bigInteger('sub_module_id');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('membership_submodules');
    }
}
