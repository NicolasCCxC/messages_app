<?php

namespace App\Infrastructure\Gateway;

use App\Enums\Payment as EnumsPayment;
use App\Enums\PayU;
use App\Exceptions\PayErrorException;
use App\Exceptions\PayUTimeOutException;
use App\Helpers\Utils;
use App\Infrastructure\Interfaces\IPaymentGateway;
use App\Infrastructure\Persistence\PaymentEloquent;
use App\Infrastructure\Services\InvoiceServices;
use App\Infrastructure\Services\NotificationServices;
use App\Models\Payment;
use App\Traits\HttpClientTrait;
use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PayUGateway implements IPaymentGateway
{

    use HttpClientTrait;

    private $url;
    private $request;
    private $apiKey;
    private $apiLogin;
    private $accountId;
    private $merchantId;
    private $publicKey;
    private $paymentEloquent;
    private $companyPaymentGateway;
    private $reportUrl;
    private $environment = [
        "local" => true,
        "testing" => true
    ];

    public function __construct(array $keys)
    {
        $this->paymentEloquent = new PaymentEloquent(new Payment(), new InvoiceServices, new NotificationServices);
        $this->companyPaymentGateway = $keys['model'];
        $this->apiKey =  $keys['credentials']['api_key'];
        $this->apiLogin =  $keys['credentials']['api_login'];
        $this->merchantId =  $keys['credentials']['merchant_id'];
        $this->publicKey =  $keys['credentials']['public_key'];
        $this->accountId = $keys['credentials']['account_id'] ?? null;

        $this->reportUrl = (array_key_exists(env('APP_ENV'),$this->environment)) ? 'https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi' : 'https://api.payulatam.com/reports-api/4.0/service.cgi';


        $this->url = (array_key_exists(env('APP_ENV'),$this->environment))
            ? 'https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi'
            : 'https://api.payulatam.com/payments-api/4.0/service.cgi';

        $this->request = [
            'language' => 'es',
            'test' => (array_key_exists(env('APP_ENV'),$this->environment)),
            'merchant' => [
                'apiLogin' => $keys['credentials']['api_login'],
                'apiKey' => $keys['credentials']['api_key'],
            ]
        ];


    }

    public function allowPaymentMethods(): array
    {
        $this->request['command'] = 'GET_PAYMENT_METHODS';

        return $this->makePost($this->url, $this->request)['paymentMethods'] ?? [];
    }

    /**
     * @throws PayErrorException
     */
    public function getPseBanks(): array
    {
        $this->request['command'] = 'GET_BANKS_LIST';
        $this->request['bankListInformation'] = [
            'paymentMethod' => 'PSE',
            'paymentCountry' => 'CO'
        ];

        $response = $this->makePost($this->url, $this->request);
        \Log::info("Response PSE Banks". json_encode($response));
        if (
            $response['error'] !== null
        ) {
            throw new PayErrorException();
        }

        return $response['banks'];
    }

    /**
     * @throws PayErrorException
     */
    public function pseTransfer(array $data, string $companyId, string $userId): array
    {

        $referenceCode = $this->getReferenceCode($data['shopping_cart']['id']);

        $transaction = [
            'order' => $this->getOrder($referenceCode, $data),
            'payer' => $this->getPayer($data),
            'extraParameters' => [
                'RESPONSE_URL' => $this->getUrlNotify($data['url']),
                'PSE_REFERENCE1' => "127.0.0.1",
                'FINANCIAL_INSTITUTION_CODE' => $data['pse']['code'],
                'USER_TYPE' => $data['pse']['user_type'],
                'PSE_REFERENCE2' => $data['pse']['user_document_type'],
                'PSE_REFERENCE3' => $data['pse']['user_document']
            ],
            'type' => 'AUTHORIZATION_AND_CAPTURE',
            'paymentMethod' => 'PSE',
            'paymentCountry' => 'CO',
            'deviceSessionId' => 'vghs6tvkcle931686k1900o6e1',
            'ipAddress' => $data['ip'],
            'cookie' => 'pt1t38347bs6jc9ruv2ecpv7o2',
            'userAgent' => $data['user_agent']
        ];

        $this->request['command'] = 'SUBMIT_TRANSACTION';
        $this->request['transaction'] = $transaction;


        $pseTransfer = $this->makePost($this->url, $this->request);

        $this->throwException($pseTransfer);

        return $this->extracted($pseTransfer, $data, EnumsPayment::LIST[EnumsPayment::METHOD_PSE], $companyId, $userId);
    }

    /**
     * @throws PayErrorException
     */
    public function creditCardTransfer(array $data, string $companyId, string $userId): array
    {

        $isValidaCreditCard = $this->luhn_check($data['credit_card']['number']);

        if(!$isValidaCreditCard){
            throw new PayErrorException();
        }

        $type = $this->checkCreditCardType($data['credit_card']['number']);

        $referenceCode = $this->getReferenceCode($data['shopping_cart']['id']);

        $transaction = [
            'order' => $this->getOrder($referenceCode, $data),
            'payer' => $this->getPayer($data),
            'creditCard' => [
                'number' => $data['credit_card']['number'],
                'securityCode' => $data['credit_card']['security_code'],
                'expirationDate' => $data['credit_card']['expiration_date'],
                'name' => $data['credit_card']['name'],
            ],
            'extraParameters' => [
                'INSTALLMENTS_NUMBER' => $data['credit_card']['dues'] ?? 1,
            ],
            'type' => 'AUTHORIZATION_AND_CAPTURE',
            'paymentMethod' => $type,
            'paymentCountry' => 'CO',
            'deviceSessionId' => 'vghs6tvkcle931686k1900o6e1',
            'ipAddress' => $data['ip'],
            'cookie' => 'pt1t38347bs6jc9ruv2ecpv7o2',
            'userAgent' => $data['user_agent']
        ];

        $this->request['command'] = 'SUBMIT_TRANSACTION';
        $this->request['transaction'] = $transaction;


        $creditCardTransfer = $this->makePost($this->url, $this->request);

        $this->throwException($creditCardTransfer);



        return $this->extracted($creditCardTransfer, $data, EnumsPayment::LIST[EnumsPayment::METHOD_CREDIT_CARD], $companyId, $userId);
    }

    public function report(string $id, string $companyId = null, string $userId = null): array
    {
        $this->request['command'] = 'TRANSACTION_RESPONSE_DETAIL';
        $this->request['details'] = [ 'transactionId' => $id ];
 
        $transaction = $this->makePost($this->reportUrl, $this->request);

        if((array_key_exists('error', $transaction) && $transaction['error']) || (array_key_exists('result', $transaction) && $transaction['result'] == null)) {
            $transaction['referenceId'] = $id;
            \Log::error('Error by response PayU', $transaction);
            return [];
        }else{
            return $this->paymentEloquent->updateStatus($companyId,$userId,$id, $transaction['result']['payload']['state'] , $transaction['result']['payload']['operationDate'])->toArray();
        }
    }

    /**
     * @throws PayErrorException
     */
    public function cashTransfer(array $data, string $companyId, string $userId): array
    {
        $referenceCode = $this->getReferenceCode($data['shopping_cart']['id']);

        $transaction = [
            'order' => $this->getOrder($referenceCode, $data),
            'payer' => $this->getPayer($data),
            "type" => "AUTHORIZATION_AND_CAPTURE",
            "paymentMethod" => $data['type'],
            "expirationDate" => Carbon::now()->addDays(15),
            "paymentCountry" => "CO",
            'deviceSessionId' => 'vghs6tvkcle931686k1900o6e1',
            'ipAddress' => $data['ip'],
            'cookie' => 'pt1t38347bs6jc9ruv2ecpv7o2',
            'userAgent' => $data['user_agent']
        ];

        $this->request['command'] = 'SUBMIT_TRANSACTION';
        $this->request['transaction'] = $transaction;

        $pseTransfer = $this->makePost($this->url, $this->request);

        $this->throwException($pseTransfer);

        return $this->extracted($pseTransfer, $data, EnumsPayment::LIST[EnumsPayment::METHOD_CASH], $companyId, $userId);
    }

    /**
     * @param array $transfer
     * @param array $data
     * @param string $paymentMethodId
     * @param string $companyId
     * @param string $userId
     * @return array
     */
    public function extracted(
        array  $transfer,
        array  $data,
        string $paymentMethodId,
        string $companyId,
        string $userId
    )
    {
        $data = [
            'reference' => $transfer['transactionResponse']['transactionId'],
            'date_approved' => EnumsPayment::STATUS[$transfer['transactionResponse']['state']] === EnumsPayment::APPROVED
                ? Carbon::now()->getTimestamp()
                : null,
            'client_id' => $data['client_id'],
            'payment_number' => $transfer['transactionResponse']['extraParameters']['REFERENCE'] ?? null,
            'url_pdf' => $transfer['transactionResponse']['extraParameters']['URL_PAYMENT_RECEIPT_PDF'] ?? null,
            'url_html' => $transfer['transactionResponse']['extraParameters']['URL_PAYMENT_RECEIPT_HTML'] ?? null,
            'amount' => $data['shopping_cart']['total_value'],
            'company_information_id' => $this->companyPaymentGateway->company_information_id,
            'company_payment_gateway_id' => $this->companyPaymentGateway->id,
            'status' => EnumsPayment::STATUS[$transfer['transactionResponse']['state']],
            'purchase_order_id' => $data['shopping_cart']['id'],
            'payment_method_id' => $paymentMethodId,
            'client_name' => $data['buyer']['full_name'] ?? null,
            'purchase_order_number' => $data['shopping_cart']['purchase_order_number'] ?? null,
        ];

        $this->paymentEloquent->store($data, $companyId, $userId);

        return $transfer;
    }

    /* Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
    * This code has been released into the public domain, however please      *
    * give credit to the original author where possible.                      */

    private function luhn_check(string $number): bool
    {
        // Set the string length and parity
        $number_length=strlen($number);
        $parity=$number_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            // Multiply alternate digits by two
            if ($i % 2 === $parity) {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit-=9;
                }
            }
            // Total up the digits
            $total+=$digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return $total % 10 === 0;

    }

    /**
     * @throws PayErrorException
     */
    private function checkCreditCardType(string $number)
    {
        try {
            $cardsPatterns = [
                'visa' => '(4\d{12}(?:\d{3})?)',
                'amex' => '(3[47]\d{13})',
                'mastercard' => '(5[1-5]\d{14})',
            ];

            $cardsName = [
                'VISA',
                'AMEX',
                'MASTERCARD'
            ];

            $matches = [];

            $pattern = "#^(?:".implode("|", $cardsPatterns).")$#";

            $result = preg_match($pattern, $number, $matches);

            return ($result>0) ? $cardsName[count($matches)-2] : false;
        }catch (Exception $e){
            throw new PayErrorException();
        }

    }

    /**
     * @param $url
     * @return string
     */
    public function getUrlNotify($url): string
    {
        return 'https://' . $url . env('ULR_NOTIFY');
    }

    /**
     * @param $shopping_cart
     * @return array[]
     */
    public function getTaxAndValue($shopping_cart): array
    {
        return [
            'TX_VALUE' => [
                'value' => $shopping_cart['total_value'],
                'currency' => 'COP'
            ],
            'TX_TAX' => [
                'value' => $shopping_cart['tax_value'],
                'currency' => 'COP'
            ],
            'TX_TAX_RETURN_BASE' => [
                'value' => ($shopping_cart['tax_value'] != 0) ? ($shopping_cart['total_value'] - $shopping_cart['tax_value']) : $shopping_cart['tax_value'],
                'currency' => 'COP'
            ]
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getBuyer(array $data): array
    {
        return [
            'fullName' => $data['buyer']['full_name'],
            'emailAddress' => $data['buyer']['email_address'],
            'contactPhone' => $data['buyer']['contact_phone'],
            'dniNumber' => $data['buyer']['dni_number'],
            'shippingAddress' => $this->getShippingAddress($data['shipping_address'])
        ];
    }

    /**
     * @param $shipping_address
     * @return array
     */
    public function getShippingAddress($shipping_address): array
    {
        return [
            'street1' => $shipping_address['street1'],
            'street2' => $shipping_address['street2'],
            'city' => $shipping_address['city'],
            'country' => $shipping_address['country'],
            'state' => $shipping_address['state'],
            'postalCode' => $shipping_address['postal_code'],
            'phone' => $shipping_address['phone'],
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getPayer(array $data): array
    {
        return [
            'emailAddress' => $data['payer']['email_address'],
            'fullName' => $data['payer']['full_name'],
            'billingAddress' => [
                'street1' => $data['billing_address']['street1'],
                'street2' => $data['billing_address']['street2'],
                'city' => $data['billing_address']['city'],
                'country' => $data['billing_address']['country'],
                'state' => $data['billing_address']['state'],
                'postalCode' => $data['billing_address']['postal_code'],
                'phone' => $data['billing_address']['phone'],
            ],
            'contactPhone' => $data['payer']['contact_phone'],
            'dniNumber' => $data['payer']['dni_number'],
        ];
    }

    /**
     * @param string $referenceCode
     * @param array $data
     * @return array
     */
    public function getOrder(string $referenceCode, array $data): array
    {
        return [
            'accountId' => $this->accountId,
            'referenceCode' => $referenceCode,
            'description' => 'Se realizara el pago de la compra',
            'language' => 'es',
            'notifyUrl' => $this->getUrlNotify($data['url']),
            'signature' => Utils::createSignatureToPayU(
                $this->apiKey,
                $this->merchantId,
                $referenceCode,
                $data['shopping_cart']['total_value'],
                'COP'
            ),
            'additionalValues' => $this->getTaxAndValue($data['shopping_cart']),
            'buyer' => $this->getBuyer($data),
            'shippingAddress' => $this->getShippingAddress($data['shipping_address']),
        ];
    }

    /**
     * @param string $id
     * @return string
     */
    private function getReferenceCode(string $id): string
    {
        return str_replace('-', '', $id) . Carbon::now()->getTimestamp();
    }

    /**
     * @param $pseTransfer
     * @throws PayErrorException
     */
    private function throwException($pseTransfer): void
    {
        if (
            $pseTransfer['error'] !== null ||
            $pseTransfer['transactionResponse'][PayU::PAYMENT_NETWORK_RESPONSE_ERROR_MESSAGE] === PayU::TIME_OUT
        ) {
            throw new PayErrorException();
        }
    }
}
