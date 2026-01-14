<?php

namespace App\Console\Commands;

use App\Models\Prefix;
use Illuminate\Console\Command;
use App\Infrastructure\Formulation\NotificationHelper;
use Illuminate\Http\Request;

class InvoiceElectronicResolutionExpirationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:InvoiceElectronicResolutionExpirationNotifications';

    /**
     * The console search description.
     *
     * @var string
     */
    protected $description = 'Check the status of resolution electronic invoice';

    /**
     * @var Prefix;
     */
    private $prefix;

    public function __construct(Prefix $prefix)
    {
        parent::__construct();
        $this->prefix = $prefix;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {  
        $startDate = Carbon::now()->format('Y-m-d') . ' 00:00:00';
        $finishDate = Carbon::now()->addDay(7)->format('Y-m-d') . ' 23:59:59';
        $prefixes = $this->prefix::whereBetween('final_validity', [$startDate, $finishDate]);
        foreach ($prefixes as $key => $prefix) {
            NotificationHelper::storeNotification($prefix, Prefix::RESOLUTION_EXPIRATION_NOTIFICATION);
        }
    }
}
