<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * For LIMITED invoicing sub‑modules (15, 60, 120, 300 documents)
     *   → sub_module_id 1, 2, 3, 4 → set expiration_date to NULL so UI shows "N/A".
     * The UNLIMITED sub‑module (id 11) keeps its expiration date unchanged.
     */
    public function up(): void
    {
        DB::table('membership_submodules')
            ->whereIn('sub_module_id', [1, 2, 3, 4])
            ->where('is_active', true)
            ->update(['expiration_date' => null]);
    }

    public function down(): void
    {
        // No rollback: NULL is the desired new state. Restoring dates requires a prior backup.
    }
};
