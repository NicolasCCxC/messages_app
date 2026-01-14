<?php

namespace App\Infrastructure\Gateway;

use App\Helpers\Utils;
use App\Traits\HttpClientTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MembershipPayment
{
    use HttpClientTrait;

    private $merchant;
    private $url;
    private $reportUrl;
    private $environment = [
        "local" => true,
        "testing" => true
    ];

    public function __construct()
    {
        $this->merchant = [
            'apiKey' => env('PAYU_API_KEY'),
            'apiLogin' => env('PAYU_API_LOGIN')
        ];

        $this->url = (array_key_exists(env('APP_ENV'),$this->environment)) ? 'https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi' : 'https://api.payulatam.com/payments-api/4.0/service.cgi';
        $this->reportUrl = (array_key_exists(env('APP_ENV'),$this->environment)) ? 'https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi' : 'https://api.payulatam.com/reports-api/4.0/service.cgi';
    }

    private function baseData(array $data){
        //TODO: set deviceSessionId, test, cookie;
        $referenceCode = str_replace('-', '', Str::uuid()->toString()) . Carbon::now()->getTimestamp();
        $data['merchant'] = $this->merchant;
        $data['language'] = 'es';
        $data['transaction']['order']['language'] = 'es';
        $data['transaction']['order']['accountId'] = env('PAYU_ACCOUNT_ID');
        $data['transaction']['order']['referenceCode'] = $referenceCode;
        $data['transaction']['order']['signature'] = Utils::createSignatureToPayU(
            env('PAYU_API_KEY'),
            env('PAYU_MERCHANT_ID'),
            $data['transaction']['order']['referenceCode'],
            $data['transaction']['order']['additionalValues']['TX_VALUE']['value'],
            'COP'
        );
        $data['transaction']['type'] = 'AUTHORIZATION_AND_CAPTURE';
        $data['transaction']['paymentCountry'] = 'CO';
        $data['transaction']['deviceSessionId'] = 'vghs6tvkcle931686k1900o6e1';
        $data['transaction']['cookie'] = 'pt1t38347bs6jc9ruv2ecpv7o2';
        $data['test'] = (array_key_exists(env('APP_ENV'),$this->environment));
        $data['command'] = 'SUBMIT_TRANSACTION';
        if(!(array_key_exists(env('APP_ENV'),$this->environment)) && isset($data['transaction']['creditCard']["securityCode"])
         && $data['transaction']['creditCard']["securityCode"] == "000") {
            unset($data['transaction']['creditCard']["securityCode"]);
        }
        return $data;
    }

    public function pse(array $data)
    {
        $data = $this->baseData($data);
        $data['command'] = 'SUBMIT_TRANSACTION';

        return $this->makePost($this->url, $data);
    }

    public function cash(array $data)
    {
        $referenceCode = str_replace('-','', 'd10f9715-13c3-4d05-88d9-33350aa5cb04').Carbon::now()->getTimestamp();
        $payment = [
            'language' => 'es',
            'command' => 'SUBMIT_TRANSACTION',
            'merchant' => $this->merchant,
            'transaction' => [
                'order' => [
                    'accountId' => '512321',
                    'referenceCode' => $referenceCode,
                    'description' => 'Se realizara el pago del carrito',
                    'language' => 'es',
                    'notifyUrl' => 'http://www.payu.com/notify', //hay que actualizarlo ahorita
                    'signature' => Utils::createSignatureToPayU(
                        env('PAYU_API_KEY'),
                        env('PAYU_MERCHANT_ID'),
                        $referenceCode,
                        $data['invoice']['total_value'],
                        'COP'
                    ),
                    'additionalValues' => [
                        'TX_VALUE' => [
                            'value' => $data['invoice']['total_value'],
                            'currency' => 'COP'
                        ],
                        'TX_TAX' => [
                            'value' => $data['invoice']['tax_value'],
                            'currency' => 'COP'
                        ],
                        'TX_TAX_RETURN_BASE' => [
                            'value' => $data['invoice']['total_value'] - $data['invoice']['tax_value'],
                            'currency' => 'COP'
                        ]
                    ],
                    'buyer' => [
                        'fullName' => $data['buyer']['full_name'],
                        'emailAddress' => $data['buyer']['email_address'],
                        'contactPhone' => $data['buyer']['contact_phone'],
                        'dniNumber' => $data['buyer']['dni_number'],
                        'shippingAddress' => [
                            'street1' => $data['shipping_address']['street1'],
                            'street2' => $data['shipping_address']['street2'],
                            'city' => $data['shipping_address']['city'],
                            'country' => $data['shipping_address']['country'],
                            'state' => $data['shipping_address']['state'],
                            'postalCode' => $data['shipping_address']['postal_code'],
                            'phone' => $data['shipping_address']['phone'],
                        ]
                    ],
                    'shippingAddress' => [
                        'street1' => $data['shipping_address']['street1'],
                        'street2' => $data['shipping_address']['street2'],
                        'city' => $data['shipping_address']['city'],
                        'country' => $data['shipping_address']['country'],
                        'state' => $data['shipping_address']['state'],
                        'postalCode' => $data['shipping_address']['postal_code'],
                        'phone' => $data['shipping_address']['phone'],
                    ],
                ],
                'payer' => [
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
                ],
                "type" => "AUTHORIZATION_AND_CAPTURE",
                "paymentMethod" => $data['type'],
                "expirationDate" => Carbon::now()->addDays(8),
                "paymentCountry" => "CO",
                'deviceSessionId' => 'vghs6tvkcle931686k1900o6e1',
                'ipAddress' => $data['ip'],
                'cookie' => 'pt1t38347bs6jc9ruv2ecpv7o2',
                'userAgent' => $data['user_agent']
            ]
        ];

        return $this->makePost($this->url, $payment);
    }

    public function paymentReport(string $transactionId)
    {
        $data = [
            'test'=> (array_key_exists(env('APP_ENV'),$this->environment)),
            'language'=> 'es',
            'command'=> 'TRANSACTION_RESPONSE_DETAIL',
            'merchant' => [
                'apiLogin' => env('PAYU_API_LOGIN'),
                'apiKey' => env('PAYU_API_KEY'),
            ],
            'details' => [
                'transactionId' => $transactionId
            ],
        ];


        return $this->makePost($this->reportUrl, $data);
    }

    public function pseBanks()
    {
        $data = [
            'test' => false,
            'language' => 'es',
            'command' => 'GET_BANKS_LIST',
            'merchant' => [
                'apiLogin' => env('PAYU_API_LOGIN'),
                'apiKey' => env('PAYU_API_KEY'),
            ],
            'bankListInformation' => [
                'paymentMethod' => 'PSE',
                'paymentCountry' => 'CO'
            ]
        ];

        return $this->makePost($this->url, $data)['banks'];
    }

    public function getCreditCardTokenId(array $data)
    {
        $data['merchant'] = $this->merchant;
        return $this->makePost($this->url, $data);
    }

    public function cashTransfer(array $data)
    {
        $data = $this->baseData($data);
        $data['command'] = 'SUBMIT_TRANSACTION';

        return $this->makePost($this->url, $data);
    }

    public function ping()
    {
        $data = [
            'test' => false,
            'language' => 'es',
            'command' => 'PING',
            'merchant' => $this->merchant
        ];

        return $this->makePost($this->url, $data);
    }

    public function deleteCardToken(array $data)
    {
        $data['language'] = 'es';
        $data['merchant'] = $this->merchant;
        $data['command'] = 'REMOVE_TOKEN';
        return $this->makePost($this->url, $data);
    }

    public function getCreditCardToken(array $data)
    {
        $data['merchant'] = $this->merchant;
        $data['language'] = 'es';
        $data['command'] = 'GET_TOKENS';

        return $this->makePost($this->url, $data);
    }

    public function paymentWithToken(array $data)
    {
        $data = $this->baseData($data);

        return $this->makePost($this->url, $data);
    }

    public function paymentWithOutToken(array $data, string $companyId)
    {
        $data = $this->baseData($data);

        return $this->makePost($this->url, $data);
    }

    public function getDetailsTransaction(array $data)
    {
        $data = [
            'test' => false,
            'language' => 'es',
            'command' => 'TRANSACTION_RESPONSE_DETAIL',
            'merchant' => $this->merchant,
            'details' => [
                'transactionId' => $data['transactionId']
            ]
        ];

        return $this->makePost($this->reportUrl, $data);
    }
}
