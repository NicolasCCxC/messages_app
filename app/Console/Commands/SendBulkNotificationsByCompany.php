<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Membership;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Infrastructure\Services\NotificationsService;

class SendBulkNotificationsByCompany extends Command
{
    public const STATE_NOTIFICATION_SEND = '1cc36b00-2b46-36cd-b306-4bff0a438baa';
    public const ELECTRONIC_INVOICE_NOTIFICATION_MODULE = '641a18d5-baa9-35ff-a2ae-daa86cdb8363';
    public const ELECTRONIC_INVOICE = 'ELECTRONIC_INVOICE';
    public const DIAN_ACCEPTED = '70beecb5-4297-4a65-912f-3292ca23ec92';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-bulk-notifications-by-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends multiple notifications at once, filtered or grouped by company.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationService = new NotificationsService();
        $companiesId = Membership::where('payment_status', Membership::PAYMENT_STATUS_APPROVED)
                        ->where('payment_method', Membership::PAYMENT_METHOD_PAYU)
                        ->whereHas('modules', function ($query) {
                            $query->where('membership_modules_id', 3)
                                ->where('is_active', true);
                        })
                        ->groupBy('company_id')
                        ->pluck('company_id');

        $companiesId->each(function ($companyId) use ($notificationService) {
            $request = [
                'type_notification_id' => self::DIAN_ACCEPTED,
                'module_notification_id' => self::ELECTRONIC_INVOICE_NOTIFICATION_MODULE,
                'type' => self::ELECTRONIC_INVOICE,
                'description' => 'Les recordamos que, de acuerdo con la normativa de la DIAN, es obligatorio diligenciar en diggi pymes, la información de nombre, tipo de documento y número de documento en el campo “Información requerida del empresario para la factura electrónica” exactamente como aparece en el RUT, para que sus documentos sean aceptados correctamente.',
                'consecutive' => 'Información importante:',
                'reference' => Str::uuid()->toString(),
                'state_notification_id' => self::STATE_NOTIFICATION_SEND,
                'date' => Carbon::now()
            ];
            $notificationService->sendNotification($request, $companyId);
        });
    }
}
