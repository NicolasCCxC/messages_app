<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MembershipSubModule;
use Illuminate\Support\Facades\DB;

class AddColumnHasModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->string('price_old')->nullable();
        });

        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->string('price_old')->nullable();
        });

        DB::table('membership_has_modules')->whereNotNull('price')->where('months','12')->update([
            'price_old' => DB::raw('price'),
            'price' => DB::raw("CAST(price AS FLOAT) * 0.70"),
        ]);

        DB::table('membership_submodules')->whereNotNull('price')->where('months','12')
        ->whereIn('sub_module_id',MembershipSubModule::SUB_MODULES_WEBSITE_IDS)->update([
            'price_old' => DB::raw('price'),
            'price' => DB::raw("CAST(price AS FLOAT) * 0.70"),
        ]);

        DB::table('membership_submodules')->whereNotNull('price')->where('months','12')
        ->whereNotIn('sub_module_id',MembershipSubModule::SUB_MODULES_WEBSITE_IDS)->update([
            'price_old' => DB::raw('price'),
            'price' => DB::raw("CAST(price AS FLOAT)"),
        ]);

        DB::table('membership_has_modules')->whereNotNull('price')->where('months','6')->update([
            'price_old' => DB::raw('price'),
            'price' => DB::raw('ROUND((CAST(price AS NUMERIC) / 2), 2)')

        ]);

        DB::table('membership_submodules')->whereNotNull('price')->where('months','6')->update([
            'price_old' => DB::raw('price'),
            'price' => DB::raw('ROUND((CAST(price AS NUMERIC) / 2), 2)')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('membership_has_modules')->whereNotNull('price')->whereIn('months',['12', '6'])->update([
            'price' => DB::raw("price_old"),
            'price_old' => null,
        ]);

        DB::table('membership_submodules')->whereNotNull('price')->whereIn('months',['12', '6'])->update([
            'price' => DB::raw("price_old"),
            'price_old' => null,
        ]);

        Schema::table('membership_has_modules', function (Blueprint $table) {
            $table->dropColumn('price_old');
        });

        Schema::table('membership_submodules', function (Blueprint $table) {
            $table->dropColumn('price_old');
        });
    }
}
