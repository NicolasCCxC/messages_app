<?php

use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const SUB_MODULE_INVENTORY_ADJUSTMENT_ID = 0;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        MembershipSubModule::with('membershipHasModule.membership')
            ->where('sub_module_id', self::SUB_MODULE_INVENTORY_ADJUSTMENT_ID)
            ->each(function ($subModule) {
                $subModule->update(['is_active' => true]);

                $subModule->membershipHasModule->update(['is_active' => true]);

                $subModule->membershipHasModule->membership->update(['is_active' => true]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
