<?php

namespace App\Infrastructure\Persistence;

use App\Enums\NotificationTypeEnum;
use App\Enums\Payment as EnumsPayment;
use App\Infrastructure\Services\InvoiceServices;
use App\Infrastructure\Services\NotificationServices;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentEloquent
{
    private $paymentModel;

    private $invoiceServices;
    private $notificationServices;

    public function __construct(Payment $payment, InvoiceServices $invoiceServices, NotificationServices $notificationServices)
    {
        $this->paymentModel = $payment;
        $this->invoiceServices = $invoiceServices;
        $this->notificationServices = $notificationServices;
    }

    public function store(
        array $data,
        string $companyId,
        string $userId
    )
    {
        $model = $this->paymentModel::create($data);
        $status = $model->status;
        if($status == EnumsPayment::APPROVED){
            $this->sendNotificationAndCreateInvoice($data, $model->status, $companyId, $userId);
        }
        return $model;

    }

    public function getAll(string $companyId)
    {
        return $this->paymentModel::whereHas('companyInformation', function (Builder $builder) use ($companyId) {
            $builder->where('company_id',$companyId);
        })
            ->fisrt();
    }

    public function get(string $id)
    {
        return $this->paymentModel::findOrFail($id);
    }

    /**
     * Update Status
     *
     * @param string companyId
     * @param string userId
     * @param string transactionId
     * @param string status
     * @param string date
     *
     */
    public function updateStatus(string $companyId, string $userId, string $transactionId, string $status, string $date = null)
    {
        $model = $this->paymentModel::where('reference', $transactionId)->first();

        $model->update(
            [
                'status' => EnumsPayment::STATUS[$status],
                'date_payment' => $date ?? null,
                'date_approved' => $date ?? null
            ]
        );

        $data = $model->toArray();
        $status = $model->status;
        $purchaseOrderId = $model->purchase_order_id;

        if($status == EnumsPayment::APPROVED){
            $purchaseOrder = $this->invoiceServices->getPurchaseOrderInformation($purchaseOrderId, $companyId, $userId);
            $data['client_name'] = $purchaseOrder['customer_name'];
            $data['purchase_order_number'] = $purchaseOrder['number_purchase_order'];
            $this->sendNotificationAndCreateInvoice($data, $status, $companyId, $userId);
        }

        if($model->wasChanged('status')){
            $this->invoiceServices->updateStatusPurchase($data, $companyId, $userId);
        }

        return $model;
    }

    public function clientIndex($clientId)
    {
        return $this->paymentModel::where('client_id',$clientId)->orderBy('created_at')->get();
    }

    /**
     * Send Notification And Create Invoice
     *
     * @param array data
     * @param string status
     * @param string companyId
     * @param string userId
     *
     * @return void
     */
    protected function sendNotificationAndCreateInvoice(array $data, string $status, string $companyId, string $userId )
    {
        $clientName = $data['client_name'] ?? null;
        $purchaseOrderNumber = $data['purchase_order_number'] ?? null;

        switch ($status) {
            case EnumsPayment::APPROVED:
                $this->invoiceServices->createInvoice($data, $companyId, $userId);
                $notificationType = NotificationTypeEnum::ACCEPTED_PURCHASE_ORDER->value;
                break;
            case EnumsPayment::PENDING:
                $notificationType = NotificationTypeEnum::PENDING_PURCHASE_ORDER->value;
                break;
            case EnumsPayment::EXPIRED:
                $notificationType = NotificationTypeEnum::EXCEEDED_PAYMENT_TIME->value;
                break;
            default:
                $notificationType = NotificationTypeEnum::EXCEEDED_PAYMENT_TIME->value;
                break;
        }

        $purchaseOrderNotification = [
            'type' => NotificationTypeEnum::PURCHASE_ORDER,
            'reference' => $data['reference'],
            'module_notification_id' => NotificationTypeEnum::MODULE_NOTIFICATIONS, // Website del cliente
            'date' => Carbon::now(),
            'user_id' => $userId ?? '',
            'company_id' => $companyId ?? '',
            'state_notification_id' => NotificationTypeEnum::NOTIFICATION_STATES['PENDING'],
            'description' => NotificationTypeEnum::fromId($notificationType)->getDetails($purchaseOrderNumber, $clientName)['description'],
            'consecutive' => NotificationTypeEnum::fromId($notificationType)->getDetails()['title'],
            'type_notification_id' => NotificationTypeEnum::fromId($notificationType)->getDetails()['type'],
        ];

        $this->notificationServices->storeNotifications($purchaseOrderNotification, $userId, $companyId);
    }
}
