<?php

namespace App\Console\Commands;

use App\Enums\Payment as EnumsPayment;
use App\Infrastructure\Gateway\GatewayFactory;
use App\Infrastructure\Gateway\HandlePayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateTransferTask extends Command
{

    private $handlePayment;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is to update state of all transfer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(HandlePayment $handlePayment)
    {
        parent::__construct();

        $this->handlePayment = $handlePayment;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Payment::with(['companyInformation', 'companyPaymentGateway'])
            ->where('status', EnumsPayment::PENDING)
            ->get()
            ->each(function (Payment $payment) {
                $credentials = $this->handlePayment->companyPaymentGatewayEloquent->decryptCredentials(
                    $payment->companyPaymentGateway->payment_gateway_id,
                    $payment->companyInformation->company_id
                );

                $gateway = GatewayFactory::createAGateway($payment->companyPaymentGateway->payment_gateway_id, $credentials);

                if(!$gateway)
                {
                    throw new BadRequestException();
                }

                $gateway->report($payment->reference, $payment->companyInformation->company_id, $payment->client_id);
            });
    }
}
