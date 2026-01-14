<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use App\Infrastructure\Formulation\MembershipHelper;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {

            // ──────────────────────────────
            // 1. Definition of FREE modules
            // ──────────────────────────────
            $freeModules = collect([
                ['id' => 1,  'expiration_date' => 12], 
                ['id' => 6,  'expiration_date' => 12], 
                ['id' => 11, 'expiration_date' => 12], 
                ['id' => 13, 'expiration_date' => 12], 
                ['id' => 5,  'expiration_date' => 12],
            ]);

            // All the modules from utils
            $allModules = MembershipHelper::getAllMembershipModules()['modules'];

            // ──────────────────────────────
            // 2. Process Active Memberships
            // ──────────────────────────────
            $memberships = Membership::where('is_active', true)->get();

            foreach ($memberships as $membership) {

                $existing = $membership->modules->pluck('membership_modules_id')->toArray();

                $missing = $freeModules->reject(fn ($m) => in_array($m['id'], $existing))->values();

                // ────────────────────────────────────
                // 2 a. If no missing modules, continue
                // ────────────────────────────────────
                foreach ($missing as $moduleData) {

                    if ($moduleData['id'] === 5) {
                        $purchasedModules = $membership->modules
                            ->whereIn('membership_modules_id', MembershipHasModules::PURCHASABLE_MODULES)
                            ->where('is_active', true)
                            ->count();

                        if ($purchasedModules < 2) {
                            continue;
                        }
                    }

                    $moduleUtils = collect($allModules)->firstWhere('id', $moduleData['id']);

                    // ────────────────────────────────────
                    // 2 b. Create the Free Modules missing
                    // ────────────────────────────────────
                    $module = MembershipHasModules::create([
                        'membership_id'          => $membership->id,
                        'membership_modules_id'  => $moduleData['id'],
                        'is_active'              => true,
                        'percentage_discount'    => 0,
                        'expiration_date'        => $membership->expiration_date,
                        'price'                  => 0,
                        'price_old'              => 0,
                        'months'                 => $moduleData['expiration_date'],
                        'name'                   => $moduleUtils['name'] ?? null,
                        'is_frequent_payment'    => false,
                        'is_cancel'              => false,
                    ]);

                    // ──────────────────────────────────
                    // 2 c. Create Submodules (if needed)
                    // ──────────────────────────────────
                    if (!empty($moduleUtils['sub_modules'])) {

                        foreach ($moduleUtils['sub_modules'] as $sub) {

                            $quantity = 0;
                            $name = $moduleUtils['name'] . ' - ' . $sub['name'];

                            if (in_array($sub['id'], MembershipHasModules::SUBMODULES_INVOICE_IDS)) {
                                $name     = MembershipHasModules::NAME_INVOICE_PLAN;
                                $quantity = $sub['quantity'] ?? 0;
                            }
                            if ($moduleUtils['id'] == MembershipHasModules::MODULE_WEB_SITE) {
                                $name = $moduleUtils['name'] . ' - ';
                            }

                            $months     = $sub['expiration_date'] ?? $moduleData['expiration_date'];
                            $priceBase  = $sub['base_price'] ?? ($months == 12 ? $sub['price_year'] : $sub['price_semester']);
                            $priceOld   = $months == 12 && isset($sub['total_discount'])
                                            ? $priceBase + $sub['total_discount']
                                            : $priceBase;

                            MembershipSubModule::create([
                                'membership_has_modules_id' => $module->id,
                                'sub_module_id'             => $sub['id'],
                                'is_active'                 => true,
                                'total_invoices'            => $quantity,
                                'remaining_invoices'        => $quantity,
                                'expiration_date'           => $membership->expiration_date,
                                'price'                     => $priceBase,
                                'price_old'                 => $priceOld,
                                'months'                    => $months,
                                'name'                      => $name,
                                'discount'                  => 0,
                            ]);
                        }
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // No need to revert
    }
};
