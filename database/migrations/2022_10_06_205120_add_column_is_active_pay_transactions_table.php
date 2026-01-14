<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsActivePayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_transactions', function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
            $table->dropUnique('pay_transactions_transaction_id_unique');
            $table->string('transaction_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_transactions', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->uuid('transaction_id')->unique()->change();
        });
    }
}
