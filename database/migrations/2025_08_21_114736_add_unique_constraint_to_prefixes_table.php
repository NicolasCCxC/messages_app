<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToPrefixesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prefixes', function (Blueprint $table) {
            $table->unique(['company_id', 'resolution_number'], 'company_resolution_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prefixes', function (Blueprint $table) {
            $table->dropUnique('company_resolution_unique');
        });
    }
}
