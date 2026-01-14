<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPasswordChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_changes', function (Blueprint $table) {
            $table->string('change_date')->change();
            $table->string('change_location')->nullable()->change();
            $table->string('change_device')->nullable()->change();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_changes', function (Blueprint $table) {
            $table->timestamp('change_date')->change();
            $table->string('change_location')->nullable(false)->change();
            $table->string('change_device')->nullable(false)->change();
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
        });
    }
}
