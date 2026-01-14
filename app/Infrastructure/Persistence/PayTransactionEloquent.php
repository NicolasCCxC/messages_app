<?php


namespace App\Infrastructure\Persistence;


use App\Helpers\MembershipCalculateHelper;
use App\Infrastructure\Services\PayService;
use App\Infrastructure\Services\UtilsService;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\PayTransaction;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;

class PayTransactionEloquent
{
    private $model;
    private $payService;
    private $membershipModel;
    private $membershipHasModuleModel;
    private $utilsService;


    public function __construct()
    {
        $this->model = new PayTransaction();
        $this->payService = new PayService();
        $this->membershipModel = new Membership();
        $this->membershipHasModuleModel = new MembershipHasModules();
        $this->utilsService = new UtilsService();
    }

    /**
     * It stores the transaction data in the database.
     *
     * @param string $companyId The id of the company that is making the payment.
     * @param string $transactionId The transaction ID from PayU
     * @param string $status The status of the transaction.
     * @param int $usersQuantity number of users that the user has purchased
     * @param int $pagesQuantity the number of pages that the user has purchased
     * @param array $dataInvoice data for create the invoice
     * @param string $membershipId the id of the membership plan that the user has chosen.
     * @param bool $isFirstPayment true if the payment is the first payment of the company, false otherwise.
     *
     * @throws GuzzleException
     */
    public function storePayTransaction(
        string $companyId,
        string $transactionId,
        string $status,
        int    $usersQuantity,
        int    $pagesQuantity,
        array  $dataInvoice,
        string $membershipId,
        bool   $isFirstPayment = false
    )
    {
        $invoicesQuantity = 0;
        if($dataInvoice !== null && array_key_exists('modules', $dataInvoice)) {
            $foundKeyInvoices = array_search($this->membershipHasModuleModel::MODULE_INVOICE_ID, array_column($dataInvoice['modules'], 'id'));
            if ($foundKeyInvoices) {
                $subModulesId = collect((collect($dataInvoice['modules'])->firstWhere('id', $this->membershipHasModuleModel::MODULE_INVOICE_ID))['sub_modules'])->flatten()->values()->toArray();
                $invoicesQuantity = collect( $this->utilsService->getSubModulesById($subModulesId))->reduce(function ($carry, $item) {
                    return $carry + $item['quantity'];
                }, 0);
            }
        }

        $this->model::create([
            'transaction_id' => $transactionId,
            'membership_id' => $membershipId,
            'company_id' => $companyId,
            'users_quantity' => $usersQuantity,
            'invoices_quantity' => $invoicesQuantity,
            'pages_quantity' => $pagesQuantity,
            'status' => $status,
            'json_invoice' => $dataInvoice && isset($dataInvoice['additional_customer_data']) ? json_encode([
                'additional_customer_data' => $dataInvoice['additional_customer_data'],
                'payer' =>  $dataInvoice["payu_data"]["transaction"]["payer"],
                'modules' =>  $dataInvoice["modules"],
                'paymentMethod' => $dataInvoice["payu_data"]["transaction"]["paymentMethod"],
                'is_first_payment' => $isFirstPayment,
                'is_immediate_purchase' => $dataInvoice["is_immediate_purchase"],
            ]) : null
        ]);
    }

    /**
     * It validates the transaction and updates the status of the transaction in the database.
     *
     * @param string $transactionId uuid The transaction ID from PayU
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function validatePendingTransaction(string $transactionId)
    {
        $membershipEloquent = new MembershipEloquent();
        $payTransaction = $this->model::where('transaction_id', $transactionId)
            ->firstOrFail();
        $response = $this->payService->getDetailTransaction(["transactionId" => $payTransaction->transaction_id])['result'];

        if (is_null($response)) {
            return "Transaction not found";
        } else {
            $response = $response['payload'];
        }
        if ($response['state'] === $this->membershipModel::PAYMENT_STATUS_APPROVED) {
            $payTransaction['json_invoice'] = json_decode($payTransaction['json_invoice'], true);
            $request = $payTransaction['json_invoice'];
            $request['users_quantity'] = $payTransaction->users_quantity;
            $request['pages_quantity'] = $payTransaction->pages_quantity;
            $request['company_id'] = $payTransaction->company_id;
            $isFirstPaymentInvoice = MembershipCalculateHelper::validateIfIsFirstInvoices($request['company_id'], $request["modules"]);

            if ($payTransaction->membership_id != null) {
                $membership = Membership::find($payTransaction->membership_id);
                $membership->update([
                    'is_active' => true
                ]);
                $membershipEloquent->activeMembership($payTransaction->membership_id);
            }

            if ($payTransaction->users_quantity > 0) {
                $membershipEloquent->addUsersMembership($payTransaction->company_id, $payTransaction->users_quantity);
            }

            if ($payTransaction->pages_quantity > 0) {
                $membershipEloquent->addPagesMembership($payTransaction->company_id, $payTransaction->pages_quantity);
            }

//            Se comentan estas lineas por un error en creacion de facturas de membresias y rechazo de las mismas por parte de DIAN
//            if(isset($request["additional_customer_data"]) )  {
//                $membershipEloquent->createPdfMembership($request["modules"],$request,$isFirstPaymentInvoice,$payTransaction->membership_id);
//            }
        }
        $payTransaction->status = $response['state'];
        $payTransaction->save();
        return $payTransaction;

    }

    /**
     * Validate if some transaction is pending by company
     *
     * @param string $company_id uuid
     * @return bool
     */
    public function validateStatusTransaction(string $company_id): bool
    {
        $transactions = $this->model::where('status', $this->membershipModel::PAYMENT_STATUS_PENDING)
        ->where('company_id', $company_id)->get();
        foreach ($transactions as $transaction) {
            $createdAt = new Carbon($transaction->created_at);
            $data = ['transactionId' => $transaction->transaction_id];
            $response = $this->payService->getDetailTransaction($data)['result'];
            if (is_null($response)) {
                continue;
            } 
            $response = $response['payload'];
            if ($response['state'] === $this->membershipModel::PAYMENT_STATUS_APPROVED) {
                continue;
            }
            $createdAt = new Carbon($transaction->created_at);
            $now = Carbon::now();
            if ($now->diffInMinutesInt($createdAt) > 15) {
                $transaction->status = $this->membershipModel::PAYMENT_STATUS_DECLINED;
            } 
            $transaction->status = $response['state'];
            $transaction->save();
        }

        return $this->model::where('company_id', $company_id)
            ->where('status', $this->membershipModel::PAYMENT_STATUS_PENDING)
            ->exists();
    }

    /**
     * Get all transactions by company
     *
     * @param string $companyId
     * @return model::all();
     */
    public function getByTransactionId(string $transactionId)
    {
        return $this->model::where('transaction_id', $transactionId)->get();
    }

    public function updateJsonPseUrlResponse(string $transactionId, array $jsonData): ?PayTransaction
    {
        $transaction = PayTransaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            return null;
        }

        $transaction->json_pse_url_response = json_encode($jsonData);
        $transaction->save();

        return $transaction;
    }
}
