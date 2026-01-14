<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\MembershipHasModules;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateModuleOneMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-module-one-memberships {--action= : Upsert, add or remove module 1 (Digitalization of physical stores) to last membership by all companies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ccxcId = '83e80ae5-affc-32b4-b11d-b4cab371c48b';
        $action = $this->option('action');
        $validateAction = in_array($action, ["add", "remove"]);

        if (!$validateAction) throw new \Exception("Invalid action");

        if ($action == "add") {
            Membership::select('id', 'company_id', 'is_active', 'initial_date', 'expiration_date', 'is_frequent_payment')
                ->where('is_active', true)
                ->orderBy('expiration_date', 'desc')
                ->get()
                ->groupBy('company_id')
                ->each(function ($memberships) {
                    $lastMembership = $memberships->first();

                    $lastMembership->modules()->updateOrCreate(
                        ['membership_modules_id' => 1],
                        [
                            'is_active' => true,
                            'membership_modules_id' => 1,
                            'percentage_discount' => 0,
                            'is_frequent_payment' => $lastMembership->is_frequent_payment,
                            'expiration_date' => Carbon::create($lastMembership->expiration_date)->format('Y-m-d'),
                            'price' => 0,
                            'months' => (int) Carbon::create($lastMembership->expiration_date)->diffInMonthsInt(Carbon::create($lastMembership->initial_date), true),
                            'name' => "Digitalización tienda física",
                            'price_old' => 0
                        ]
                    );
                });
        } elseif ($action == "remove") {
            MembershipHasModules::where('membership_modules_id', 1)
                ->whereHas('membership', function ($query) use ($ccxcId) {
                    $query->where('company_id', '!=', $ccxcId);
                })
                ->delete();
        }
    }
}
