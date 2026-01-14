<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsHasModules extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->string('price_year')->nullable();
            $table->bigInteger('months')->nullable();
            $table->string('name')->nullable();
        });

        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->string('price_year')->nullable();
            $table->bigInteger('months')->nullable();
            $table->string('name')->nullable();
        });
    }   
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->dropColumn('price_year');
            $table->dropColumn('months');
            $table->dropColumn('name');
        });

        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->dropColumn('price_year');
            $table->dropColumn('months');
            $table->dropColumn('name');
        });
    }
}
