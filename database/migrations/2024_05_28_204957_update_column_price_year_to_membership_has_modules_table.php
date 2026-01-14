<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->renameColumn('price_year', 'price');
        });
        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->renameColumn('price_year', 'price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->renameColumn('price', 'price_year');
        });
        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->renameColumn('price', 'price_year');
        });
    }
};
