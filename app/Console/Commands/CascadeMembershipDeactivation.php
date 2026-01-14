<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\MembershipSubModule;
use Symfony\Component\Console\Command\Command as SymfonyCommand;


class CascadeMembershipDeactivation extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'deactivate:membership-cascade';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Deactivates memberships, modules and submodules if they are inactive';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $submodules = MembershipSubModule::where('is_active', true)->get();

        $this->info("Processing {$submodules->count()} active submodules...");

        $count = 0;

        foreach ($submodules as $submodule) {
            if ($this->checkAndDeactivateSubModule($submodule)) {
                $count++;
            }
        }

        $this->info("Deactivated submodules: {$count}");
        $this->info("Process completed.");

        return SymfonyCommand::SUCCESS;
    }

    /**
     * Deactivates a submodule if the conditions are met
     * and cascade verifies if should deactivbate the module and membership
     */
    public function checkAndDeactivateSubModule(MembershipSubModule $submodule): bool
    {
        if (
            $submodule->remaining_invoices === 0 &&
            $submodule->is_active &&
            in_array($submodule->sub_module_id, $submodule::DEACTIVABLE_SUB_MODULES)

            ) {
            $submodule->is_active = false;
            $submodule->save();

            $this->info("Submodule {$submodule->id} deactivated.");
            $this->checkAndDeactivateModule($submodule->membershipHasModule);
            return true;
        }

        return false;
    }

    /**
     * verifies if all the submodules are inactive and deactivates the module if applies
     */
    public function checkAndDeactivateModule(?MembershipHasModules $module): void
    {
        if (!$module) return;

        $exceptions = $module::NON_DEACTIVABLE_MODULES;

        if (in_array($module->membership_modules_id, $exceptions)) return;

        $allSubmodulesInactive = $module->membershipSubmodules->every(
            fn($sub) => !$sub->is_active
        );

        if ($allSubmodulesInactive && $module->is_active) {
            $module->is_active = false;
            $module->save();

            $this->info("Module {$module->id} deactivated.");
            $this->checkAndDeactivateMembership($module->membership);
        }
    }

    /**
     * Verifies if all the modules are inactive and deactivates the membership if applies
     */
    public function checkAndDeactivateMembership(?Membership $membership): void
    {
        if (!$membership) return;

        $allModulesInactive = $membership->modules->every(
            fn($mod) => !$mod->is_active
        );

        if ($allModulesInactive && $membership->is_active) {
            $membership->is_active = false;
            $membership->save();

            $this->info("Membership {$membership->id} Deactivated.");
        }
    }
}
