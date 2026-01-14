<?php

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    const MODULE_ELECTRONIC_DOCUMENTS_ID = 3;
    const SUBMODULE_COMPENSATED_DOCUMENTS_ID = 0; // Submodule ID used exclusively to compensate electronic invoicing issues

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get active memberships that include the electronic documents module
        $memberships = Membership::where('is_active', true)
            ->whereHas('modules', function ($q) {
                // Filter memberships by module electronic document ID
                $q->where('membership_modules_id', self::MODULE_ELECTRONIC_DOCUMENTS_ID);
            })
            ->orderBy('initial_date', 'desc') // Sort by newest initial date
            ->get();

        // Group by company and keep only the latest membership for each
        $latestMemberships = $memberships->groupBy('company_id')->map(function ($group) {
            return $group->first();
        });

        foreach ($latestMemberships as $membership) {

            // Check if this membership has the required module
            $membershipHasModule = $membership->modules()
                ->where('membership_modules_id', self::MODULE_ELECTRONIC_DOCUMENTS_ID)
                ->where('is_active', true)
                ->whereHas('membershipSubmodules', function ($q) {
                    $q->where('remaining_invoices', '>', 0);
                })
                ->first();

            if (!$membershipHasModule) {
                continue; // Skip if module is missing
            }

            // Create submodule with 6 compensated electronic documents
            $membershipHasModule->membershipSubmodules()->create([
                'id' => Str::uuid(),
                'sub_module_id' => self::SUBMODULE_COMPENSATED_DOCUMENTS_ID,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'total_invoices' => 6,
                'remaining_invoices' => 6,
                'expiration_date' => Carbon::parse($membershipHasModule->expiration_date)->format('Y-m-d'),
                'price' => 0,
                'name' => 'Documentos electrónicos - Compensación de 6 documentos electrónicos por mantenimiento del 26 de noviembre 2025',
                'price_old' => 0,
                'discount' => 0,
            ]);

            // Console output for traceability
            echo "\nAdded 6 document submodule to company ID: {$membership->company_id}";
        }

        // Final console message
        echo "\nCompleted adding 6 document submodules for maintenance on November 26, 2025.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Notify rollback start
        echo "\nRemoving 6 document submodules added for maintenance on November 26, 2025...\n";

        // Remove created submodules by name
        \DB::table('membership_submodules')
            ->where('name', 'Documentos electrónicos - Compensación de 6 documentos electrónicos por mantenimiento del 26 de noviembre 2025')
            ->delete();
    }
};
