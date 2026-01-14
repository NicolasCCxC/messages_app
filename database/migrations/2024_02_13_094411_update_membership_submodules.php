<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMembershipSubmodules extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->bigInteger('total_invoices')->nullable();
            $table->bigInteger('remaining_invoices')->nullable();
            $table->string('expiration_date')->nullable();
        });
    }   
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->dropColumn('total_invoices');
            $table->dropColumn('remaining_invoices');
            $table->dropColumn('expiration_date');
        });
    }
}
