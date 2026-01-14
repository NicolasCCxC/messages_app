<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Membership;

class UpdateMembershipPurchaseDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $memberships = [
            [
                'id' => 'f60b6237-968c-4f71-a370-954f8e95bd44',
                'initial_date' => '2023-09-19',
            ],
            [
                'id' => '78324e1d-2e02-41e5-9f5b-687d1fe94af3',
                'initial_date' => '2023-10-18',
            ],
            [
                'id' => '4e025762-62e2-4d3d-9ca6-77af00333553',
                'initial_date' => '2023-09-14',
            ]
        ];

        foreach ($memberships as $membership) {
            $model = Membership::query()->find($membership["id"]);
            if ($model) {
                $model->update([
                    'initial_date' => $membership["initial_date"],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $memberships = [
            [
                'id' => 'f60b6237-968c-4f71-a370-954f8e95bd44',
                'initial_date' => '2023-09-18',
            ],
            [
                'id' => '78324e1d-2e02-41e5-9f5b-687d1fe94af3',
                'initial_date' => '2023-10-17',
            ],
            [
                'id' => '4e025762-62e2-4d3d-9ca6-77af00333553',
                'initial_date' => '2023-08-12',
            ],
        ];

        foreach ($memberships as $membership) {
            $model = Membership::query()->find($membership["id"]);
            if ($model) {
                $model->update([
                    'initial_date' => $membership["initial_date"],
                ]);
            }
        }
    }
}
