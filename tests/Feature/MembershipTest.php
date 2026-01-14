<?php

namespace Tests\Feature;

use App\Infrastructure\Services\InvoiceService;
use App\Models\Company;
use App\Models\Membership;
use App\Models\MembershipHasModules;
use App\Models\Prefix;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class MembershipTest extends TestCase
{
    /**
     * It avoids broken test that MembershipTest class
     *
     * @return void
     */
    public function test_example_membership()
    {
        $this->assertTrue(true);
    }

   use RefreshDatabase;

   /**
    * @var Company
    */
   private $company;

   /**
    * @var Membership
    */
   private $membership;

   const ADDITIONAL_CUSTOMER_DATA = [
       "document_type" => "f73f5793-795e-33db-9115-95437f9ecaea",
       "type_taxpayer_id" => "c8dfbea8-11ca-35bb-bea2-3dc15b66af64",
       "type_taxpayer_name" => "Persona Jurídica",
       "tax_details_code" => "01",
       "tax_details_name" => "IVA",
       "fiscal_responsibilities" => [["id" => 1]],
       "country_id" => 46,
       "department_id" => 5,
       "city_id" => "149",
       "card_type" => null,
   ];

   const PAYU_DATA_PSE =  [
       "transaction" => [
           "order" => [
               "description" => "Payment test description",
               "notifyUrl" => "http://www.payu.com/notify",
               "buyer" => [
                   "fullName" => "First name and second buyer name",
                   "emailAddress" => "buyer_test@test.com",
                   "contactPhone" => "7563126",
                   "dniNumber" => "123456789",
                   "shippingAddress" => [
                       "street1" => "calle 100",
                       "street2" => "5555487",
                       "city" => "Medellin",
                       "state" => "Antioquia",
                       "country" => "CO",
                       "postalCode" => "000000",
                       "phone" => "7563126"
                   ]
               ],
               "shippingAddress" => [
                   "street1" => "calle 100",
                   "street2" => "5555487",
                   "city" => "Medellin",
                   "state" => "Antioquia",
                   "country" => "CO",
                   "postalCode" => "0000000",
                   "phone" => "7563126"
               ]
           ],
           "payer" => [
               "fullName" => "First name and second payer name",
               "emailAddress" =>  "payer_test@test.com",
               "contactPhone" => "7563126",
               "dniNumber" => "5415668464",
               "billingAddress" => [
                   "street1" => "calle 93",
                   "street2" => "125544",
                   "city" => "Bogota",
                   "state" => "Bogota DC",
                   "country" => "CO",
                   "postalCode" => "000000",
                   "phone" => "7563126"
               ]
           ],
           "extraParameters" => [
               "RESPONSE_URL" => "http://www.payu.com/notify",
               "PSE_REFERENCE1" => "127.0.0.1",
               "FINANCIAL_INSTITUTION_CODE" => "1022",
               "USER_TYPE" => "N",
               "PSE_REFERENCE2" => "CC",
               "PSE_REFERENCE3" => "123456789"
           ],
           "paymentMethod" => "PSE",
           "deviceSessionId" => "vghs6tvkcle931686k1900o6e1",
           "cookie" => "pt1t38347bs6jc9ruv2ecpv7o2",
           "userAgent" => "Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
       ]
   ];

   const PAYU_DATA_WITHOUT_TOKEN = [
           "transaction" => [
               "order" => [
                   "description" => "Payment test description",
                   "notifyUrl" => "http://www.payu.com/notify",
                   "buyer" => [
                       "merchantBuyerId" => "1",
                       "fullName" => "First name and second buyer name",
                       "emailAddress" => "buyer_test@test.com",
                       "contactPhone" => "7563126",
                       "dniNumber" => "123456789",
                       "shippingAddress" => [
                           "street1" => "calle 100",
                           "street2" => "5555487",
                           "city" => "Medellin",
                           "state" => "Antioquia",
                           "country" => "CO",
                           "postalCode" => "000000",
                           "phone" => "7563126"
                       ]
                   ],
                   "shippingAddress" => [
                       "street1" => "calle 100",
                       "street2" => "5555487",
                       "city" => "Medellin",
                       "state" => "Antioquia",
                       "country" => "CO",
                       "postalCode" => "0000000",
                       "phone" => "7563126"
                   ]
               ],
               "payer" => [
                   "merchantPayerId" => "10",
                   "fullName" => "First name and second payer name",
                   "emailAddress" => "payer_test@test.com",
                   "contactPhone" => "7563126",
                   "dniNumber" => "541566846",
                   "billingAddress" => [
                       "street1" => "calle 93",
                       "street2" => "125544",
                       "city" => "Bogota",
                       "state" => "Bogota DC",
                       "country" => "CO",
                       "postalCode" => "000000",
                       "phone" => "7563126"
                   ]
               ],
               "creditCard" => [
                   "name" => "APPROVED",
                   "number" => "4037997623271984",
                   "expirationDate" => "2027/01",
                   "securityCode" => "123"
               ],
               "paymentMethod" => "VISA",
               "deviceSessionId" => "vghs6tvkcle931686k1900o6e1",
               "cookie" => "pt1t38347bs6jc9ruv2ecpv7o2",
               "userAgent" => "Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
           ],
           "test" => true
   ];

   const PAYU_DATA_CREATE_TOKEN = [
       "transaction" => [
           "order" => [
               "description" => "Payment test description",
               "notifyUrl" => "http://www.payu.com/notify",
               "buyer" => [
                   "merchantBuyerId" => "1",
                   "fullName" => "First name and second buyer name",
                   "emailAddress" => "buyer_test@test.com",
                   "contactPhone" => "7563126",
                   "dniNumber" => "123456789",
                   "shippingAddress" => [
                       "street1" => "calle 100",
                       "street2" => "5555487",
                       "city" => "Medellin",
                       "state" => "Antioquia",
                       "country" => "CO",
                       "postalCode" => "000000",
                       "phone" => "7563126"
                   ]
               ],
               "shippingAddress" => [
                   "street1" => "calle 100",
                   "street2" => "5555487",
                   "city" => "Medellin",
                   "state" => "Antioquia",
                   "country" => "CO",
                   "postalCode" => "0000000",
                   "phone" => "7563126"
               ]
           ],
           "payer" => [
               "merchantPayerId" => "10",
               "fullName" => "First name and second payer name",
               "emailAddress" => "mfbarreto@ccxc.us",
               "contactPhone" => "7563126",
               "dniNumber" => "5415668464",
               "billingAddress" => [
                   "street1" => "calle 93",
                   "street2" => "125544",
                   "city" => "Bogota",
                   "state" => "Bogota DC",
                   "country" => "CO",
                   "postalCode" => "000000",
                   "phone" => "7563126"
               ]
           ],
           "creditCard" => [
               "payerId" => "10",
               "name" => "APPROVED",
               "identificationNumber" => "32144457",
               "paymentMethod" => "VISA",
               "number" => "4037997623271984",
               "expirationDate" => "2025/05",
               "securityCode" => "777"
           ],
           "paymentMethod" => "VISA",
           "paymentCountry" => "CO",
           "deviceSessionId" => "vghs6tvkcle931686k1900o6e1",
           "cookie" => "pt1t38347bs6jc9ruv2ecpv7o2",
           "userAgent" => "Mozilla/5.0 (Windows NT 5.1; rv:18.0) Gecko/20100101 Firefox/18.0"
       ],
       "test" => true
   ];


   /**
    * @test
    */
   public function it_should_get_company_membership_status()
   {
       $this->initTestData();
       $response = $this->json('GET', "/api/company/membership");
       $response->assertStatus(200);
       $response->assertJsonStructure([
           'message',
           'statusCode',
           'service',
           'data' => [
               'active_membership',
               'company_memberships'
           ]
       ]);
   }

   /**
    * Cancel modules membership
    * @test
    */
   public function it_should_cancel_memberships(){
       $this->initTestData();
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $company_id = Company::COMPANY_CCXC,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
                ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 6]]],
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "payu_data" => self::PAYU_DATA_CREATE_TOKEN,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       $membership = Membership::with('modules')->where('company_id', $company_id)->get()->first();
       $modules_id = $membership->modules->pluck('id')->toArray();
       $data = [
           "password" => "password",
           "reason_cancellation" => "Tengo dificultades para manejar diggi Pymes",
           "modules_id" => $modules_id
       ];
       $response = $this->json('POST', 'api/company/memberships/cancel-memberships', $data);
       $response->assertStatus(200);
       $response->assertJsonStructure([
           'message',
           'statusCode',
           'service',
           'data' => [
               'id',
               'purchase_date',
               'initial_date',
               'expiration_date',
               'is_active',
               'company_id',
               'is_first_payment',
               'is_frequent_payment',
               'payment_status',
               'payment_method',
               'total',
               'invoice_pdf',
               'modules',
               'invoice_credit_note_id',
               'invoice_credit_note_pdf'
           ]
       ]);
   }

   /**
    * @test
    */
   public function it_should_download_membership_invoice()
   {
       $this->initTestData();
       $this->withoutExceptionHandling();
       $request = [
           'user_id' => auth()->id(),
           'membership_id' => $this->membership->id
       ];
       $response = $this->json('POST', 'api/company/membership/invoice', $request);
       $response->assertStatus(200);
   }

   /**
    * test to create token payu
    * @test
    */
   public function it_should_pay_and_create_token()
   {
       $this->initTestData();
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]]
           ],
           "payu_data" => self::PAYU_DATA_CREATE_TOKEN
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);
       $this->assertDatabaseCount('memberships', 3);
       $this->assertDatabaseCount('membership_has_modules', 14 +  count(MembershipHasModules::FREE_MODULES));
       $this->assertDatabaseCount('membership_submodules', 1);

   }

   /**
    * test to pay and create token PayU
    * test to get pages available for a company
    * @test
    */
   public function it_should_get_pages_available()
   {
       $company_id = Company::COMPANY_CCXC;
       $this->initTestData();
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $company_id,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
           "users_quantity" => 0,
           "pages_quantity" => 3,
           "modules" => [
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]]
           ],
           "payu_data" => self::PAYU_DATA_CREATE_TOKEN
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       $response = $this->json('GET', "api/company/memberships/pages-available", []);
       $response->assertStatus(200);
       $this->assertDatabaseHas('companies',[
           'id' => $company_id,
           'pages_available' => json_decode($response->getContent())->data
       ]);
   }

   /**
    * Pay membership whit pse
    * @test
    */
   public function it_should_pay_membership_pse()
   {
       $this->initTestData();

       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
               ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 6]]],
               ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
               ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "payu_data" => self::PAYU_DATA_PSE,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-pse', $data);
       $response->assertStatus(200);
       $this->assertDatabaseCount('memberships', 3);
       $this->assertDatabaseCount('membership_has_modules', 16 +  count(MembershipHasModules::FREE_MODULES));
       $this->assertDatabaseCount('membership_submodules', 2);
       $this->assertDatabaseHas('pay_transactions', [
           'company_id' => $this->company->id,
           'status' => json_decode($response->getContent())->data->transactionResponse->state
       ]);
   }

   /** @test */
   public function it_should_update_status_transaction()
   {
       $this->initTestData();
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "users_quantity" => 0,
           "pages_quantity"=> 0,
           "modules" => [
                ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 6]]],
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "payu_data" => self::PAYU_DATA_PSE,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
       ];
       $response = $this->json('POST', 'api/company/memberships/pay-pse', $data);

       $response->assertStatus(200);

       $transactionId = json_decode($response->getContent())->data->transactionResponse->transactionId;

       $response = $this->json('GET', "api/company/memberships/update-status-pay/{$transactionId}");

       $response->assertStatus(200);

       $dataResponse = (json_decode($response->getContent())->data);

       if($dataResponse->status == 'APPROVED'){
           $this->assertDatabaseHas('memberships', [
               'id' => $dataResponse->membership_id,
               'is_active' => true
           ]);
       }

       if($dataResponse->status == 'DECLINED'){
           $this->assertDatabaseHas('memberships', [
               'id' => $dataResponse->membership_id,
               'is_active' => false
           ]);
       }
       $this->assertDatabaseHas('pay_transactions', [
           'transaction_id' => $transactionId,
           'status' => $dataResponse->status
       ]);

       $data = [
           "company_id" => $this->company->id,
           "users_quantity" => 1,
           "pages_quantity" => 0,
           "is_immediate_purchase" => true,
           "modules" => [],
           "payu_data" => self::PAYU_DATA_PSE,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA
       ];
       $response = $this->json('POST', 'api/company/memberships/pay-pse', $data);

       $response->assertStatus(200);

       $transactionId = json_decode($response->getContent())->data->transactionResponse->transactionId;

       $usersAvailable = $this->company->users_available;

       $response = $this->json('GET', "api/company/memberships/update-status-pay/{$transactionId}");

       $response->assertStatus(200);

       $dataResponse = (json_decode($response->getContent())->data);

       if($dataResponse->status == 'APPROVED'){
           $this->assertDatabaseHas('companies', [
               'id' => $dataResponse->company_id,
               'users_available' => $usersAvailable + 1
           ]);
       }

       if($dataResponse->status == 'DECLINED'){
           $this->assertDatabaseHas('companies', [
               'id' => $dataResponse->company_id,
               'users_available' => "{$usersAvailable}"
           ]);
       }

       $this->assertDatabaseHas('pay_transactions', [
           'transaction_id' => $transactionId,
           'status' => $dataResponse->status
       ]);

   }
   /** @test */
   public function it_should_validate_membership_module(){
       $this->initTestData();
       MembershipHasModules::factory()->create([
           'membership_id' => $this->membership->id,
           'membership_modules_id' => 1,
           'is_active' => true
       ]);
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
       ];
       $response = $this->json('POST', 'api/company/memberships/validate-modules', $data);
       $statusCode = json_decode($response->getContent())->data->statusCode;
       $this->assertEquals(400, $statusCode);
   }

   /** @test */
   public function it_should_if_company_can_purchase_more_modules_membership()
   {
       $this->initTestData();

       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
           ],
           "payu_data" => self::PAYU_DATA_CREATE_TOKEN
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       // when the company hasn't some transactions pending
       $response = $this->json('GET', "api/company/memberships/validate-status-transaction", [], ['company_id' => $this->company->id]);
       $response->assertStatus(200);
       $dataResponse = (json_decode($response->getContent())->data);
       $this->assertEquals(false, $dataResponse);

       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "users_quantity" => 0,
           "pages_quantity"=> 0,
           "modules" => [
                ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 6]]],
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "payu_data" => self::PAYU_DATA_PSE,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-pse', $data);
       $response->assertStatus(200);
   }

   /**
    * @test
    */
   public function it_should_get_active_and_inactive_memberships()
   {
       $this->initTestData();
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "payu_data" => self::PAYU_DATA_CREATE_TOKEN
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       $data["modules"] = [
            ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 6]]],
            ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
            ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
       ];
       $data["users_quantity"] = 1;
       $data["pages_quantity"] = 3;

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       $response = $this->json('GET', "api/company/memberships/details");
       $response->assertStatus(200);
       $response->assertJsonStructure([
           'message',
           'statusCode',
           'service',
           'data' => [
               'active_memberships' => [],
               'inactive_memberships' => [],
           ]
       ]);
   }


   /**
    * @test
    */
   public function it_should_get_data_binnacle_memberships()
   {
       $this->initTestData();
       $data = [
           "is_immediate_purchase" => false,
           "company_id" => $this->company->id,
           "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
           "users_quantity" => 0,
           "pages_quantity" => 0,
           "modules" => [
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
           ],
           "payu_data" => self::PAYU_DATA_CREATE_TOKEN
       ];

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       $data["modules"] = [
            ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 6]]],
            ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
            ["id" => 4, "sub_modules" => [], 'expiration_date' => 6]
       ];
       $data["users_quantity"] = 1;
       $data["pages_quantity"] = 3;

       $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
       $response->assertStatus(200);

       $response = $this->json('GET', "api/company/memberships/binnacle");
       $response->assertStatus(200);
       $response->assertJsonStructure([
           'message',
           'statusCode',
           'service',
           'data' => [
               'type',
               'module',
               'start_date',
               'end_date',
               'data' => [],
           ]
       ]);


   }

   private function initTestData()
   {
       $this->signIn();
       $this->company = Company::factory()->create();

       $this->membership = Membership::factory()->create([
           'company_id' => $this->company->id,
           'payment_method' => 'FREE',
           'is_active' => true,
           'payment_status' => 'APPROVED',
       ]);

       $invoiceService = new InvoiceService();
       $response = $invoiceService->getLastConsecutiveByPrefix(Company::COMPANY_CCXC,['without_prefixes' => true])['data'];
       $this->assertNotEmpty($response);
       foreach ($response as $object) {
           Prefix::factory()->create([
               'id' => $object['prefix_id'],
               'company_id' => Company::COMPANY_CCXC,
               'type' => Prefix::INVOICE
           ]);
       }
   }

   /**
    * @test_
    */
    public function it_should_recurrent_payment_memberships()
    {
        $this->initTestData();
        $data = [
            "is_immediate_purchase" => false,
            "company_id" => Company::COMPANY_CCXC,
            "additional_customer_data" => self::ADDITIONAL_CUSTOMER_DATA,
            "users_quantity" => 0,
            "pages_quantity" => 0,
            "is_frequent_payment" => true,
            "modules" => [
                ["id" => 2, "sub_modules" => [["id" => 5, 'expiration_date' => 12]]],
                ["id" => 3, "sub_modules" => [["id" => 1, 'expiration_date' => 12]]],
                ["id" => 4, "sub_modules" => [], 'expiration_date' => 12]
            ],
            "payu_data" => self::PAYU_DATA_CREATE_TOKEN
        ];

        $response = $this->json('POST', 'api/company/memberships/pay-create-token', $data);
        $response->assertStatus(200);
        $this->assertDatabaseCount('memberships', 3);
        $this->artisan('pay:recurrent-payment-membership')->assertExitCode(0);
        $this->assertDatabaseCount('memberships', 4);
    }
}
