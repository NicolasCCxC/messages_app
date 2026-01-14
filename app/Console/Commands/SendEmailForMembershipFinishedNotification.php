<?php

namespace App\Console\Commands;

use App\Infrastructure\Services\NotificationsService;
use App\Models\Membership;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendEmailForMembershipFinishedNotification extends Command
{
    private $membershipModel;
    private $notificationsService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:send-email-for-membership-finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email for membership finished';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Membership $membershipModel, NotificationsService $notificationsService)
    {
        parent::__construct();
        $this->membershipModel = $membershipModel;
        $this->notificationsService = $notificationsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');

        $data = $this->membershipModel::with(['modules' => function ($query) use($today){
            $query->whereBetween('expiration_date', [$today, Carbon::now()->addDays(3)]);
        }])->where('payment_method', '!=', null)
            ->where('is_active', true)
            ->where('is_frequent_payment', true)
            ->get();

        $data->each(function ($membership) use ($today) {
            $membership->modules->each(function($module) use ($membership, $today){
                $expirationDay = Carbon::parse($module->expiration_date);
                $diffDays = Carbon::parse($expirationDay)->diffInDaysInt($today);
                if ( ($diffDays == 2 && $membership->payment_method == $this->membershipModel::PAYMENT_METHOD_FREE)
                    || ($diffDays == 0 && $membership->payment_method == $this->membershipModel::PAYMENT_METHOD_PAYU) ) {

                    $dataNotification = [
                        'company_id' => $membership->company_id,
                        'company_name' => $membership->company->name,
                        'company_email' => $membership->company->role()->where('name', 'Super Administrador')->first()->users()->first()->email,
                        'type' => $membership->payment_method,
                    ];

                    $this->notificationsService->sendEmailForMembershipFinished($dataNotification);
                }
            });
        });
    }
}
