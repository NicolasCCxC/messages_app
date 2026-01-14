<?php


namespace App\Infrastructure\Formulation;

use App\Helpers\TransformArrayHelper;
use App\Http\Requests\GateRequest;
use App\Infrastructure\Persistence\GateEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GatewayHelper
{
    use ResponseApiTrait;

    /**
     * Resources allowed on upload files
     */
    private const RESOURCE_UPLOAD_VOUCHER = '/invoices/vouchers/upload-voucher';
    private const RESOURCE_UPLOAD_CERTIFICATE = '/api/electronic-invoice/configuration';
    private const RESOURCE_UPLOAD_FILE = '/bucket/upload-file';
    private const RESOURCE_UPLOAD_IMAGES = '/websites/images/upload';
    private const RESOURCE_UPLOAD_FILE_UPDATE = '/bucket/upload-file/update';
    private const RESOURCE_SEND_ELECTRONIC_DOCUMENT_CUSTOMER = '/notifications/electronic-invoice/send-electronic-document';
    private const RESOURCE_SEND_ACCEPTATION_RESPONSE = '/notifications/electronic-invoice/send-acceptation-notification';
    private const RESOURCE_SEND_EMAIL = '/notifications/send-email';
    private const RESOURCE_UPLOAD_INSTALLMENT_ATTACHMENT = '/invoices/money-installments/update-money-installment';
    private const RESOURCE_UPLOAD_NOTE_SUPPORT = '/invoices/bills/suppliers/purchase/support-notes';
    private const RESOURCE_UPLOAD_REJECT_SUPPORT = '/invoices/rejected-invoices/support';
    private const RESOURCE_BILLS_SEND_EMAIL = '/invoices/bills/send-email';
    private const RESOURCE_HELP_CENTER_SEND_EMAIL = '/notifications/help-center';
    private const RESOURCE_BINNACLE_HELP_CENTER_SEND_EMAIL = '/attention-records';
    /**
     * @param $request
     * @return array|mixed|StreamedResponse
     * @throws GuzzleException
     */
    public static function routeHandler($request)
    {
        try {
            $path = GateEloquent::getPath($request['service']);
            $serviceToken = $path->token;
            $client = new Client([
                'base_uri'        => $path->description,
                'timeout'         => config('app.timeout'),        
                'connect_timeout' => config('app.timeout'),        
                'read_timeout'    => config('app.timeout'),        
            ]);
            $url = $path->description . $request['resource'];
            $basicHeaders = [
                'user-id' => $request['user_id'],
                'company-id' => $request['company_id']
            ];

            // Redirect to handle for http post on bucket module
            $urlIgnoreBucket = [
                '/document/electronic-invoice/invoice-preview',
                '/bucket/list',
                '/bucket/politics/upload',

                'document/electronic-invoice/invoice-preview',
                'bucket/list',
                'bucket/politics/upload',

                'document/electronic-invoice/invoice-preview/',
                'bucket/list/',
                'bucket/politics/upload/',

                '/document/electronic-invoice/invoice-preview/',
                '/bucket/list/',
                '/bucket/politics/upload/',
            ];
            if ($request['service'] === Module::BUCKET && $request['method'] === 'POST' && !in_array($request['resource'], $urlIgnoreBucket)) {
                return self::handleDownloadFile($client, $request, $serviceToken);
            }

            // Redirect to handle for http get on electronic-invoice module
            else if ($request['service'] === Module::ELECTRONIC_INVOICE && array_key_exists('file_type', $request['data'] ?? [])) {
                return self::handleDownloadFile($client, $request, $serviceToken);
            }

            if (Str::contains($request['resource'], 'electronic-invoice/get-zip') !== false) {
                $response = Http::withToken($serviceToken)
                    ->withHeaders($basicHeaders)
                    ->get($url);
                $contentDisposition = explode(';', $response->header('Content-Disposition'));
                $fileName = explode('=' ,$contentDisposition[1])[1];
                Storage::disk('temp')->put($fileName, $response->body());
                return Storage::download("tmp/$fileName", $fileName);
            }

            switch ($request['method']) {
                case 'GET':
                    if ($request['data'] === []) {
                        return Http::withToken($serviceToken)
                            ->withHeaders($basicHeaders)
                            ->get($url)
                            ->json();
                    } else {
                        return json_decode(
                        // With Data
                            $client->request(
                                'GET',
                                $url,
                                [
                                    'json' => $request['data'],
                                    'headers' => array_merge($basicHeaders, [
                                        'Authorization' => "Bearer {$serviceToken}"
                                    ])
                                ])->getBody()
                            , true);
                    }
                case 'POST':
                case 'PUT':
                case 'DELETE':
                    $methodName = strtolower($request['method']);
                    return json_decode($client->request(
                        $methodName,
                        $url,
                        [
                            'json' => $request['data'], // datos enviados en el body
                            'headers' => array_merge($basicHeaders, [
                                'Authorization' => "Bearer {$serviceToken}",
                                'Accept' => 'application/json',
                            ])
                        ]
                    )->getBody(), true);
                default:
                    abort(Response::HTTP_UNAUTHORIZED);
                    return [];
            }
        }catch (ClientException $exception){
            return json_decode($exception->getResponse()->getBody()->getContents());
        }
        catch (\Exception $exception) {
            Log::info("Error on GatewayHelper-handler: " . $exception->getMessage());
            abort(Response::HTTP_BAD_REQUEST, 'Error on Gateway-handler');
            return [];
        }
    }

    /**
     * @param $request
     * @return array|mixed|StreamedResponse
     * @throws GuzzleException
     */
    public static function authorizedRoutes($request)
    {
        $authorizedRoutes = [
            '/utils/countries',
            '/utils/departments',
            '/utils/departments/countries/',
            '/utils/cities',
            '/utils/cities/departments/',
            '/utils/document-types',
            '/utils/membership-types',
            '/utils/ciius',
            '/utils/unit-measurements',
            '/utils/payment-methods',
            '/utils/reason-rejections',
            '/utils/membership-modules',
            '/notifications/email-contact-us-website/',
            '/notifications/contact-lading',
            '/websites/blog-articles/',
            '/websites/blog-articles/comment/',
            '/inventories/products/company/catalog',
            '/inventories/categories/',
            '/inventories/unique-products/datasheet/',
            '/invoices/bills/electronic/accept-electronic-document-by-customer',
            '/invoices/bills/download-invoice/',
            '/invoices/rejected-invoices/support',
            '/invoices/rejected-invoices',
            '/document/download',
            '/inventories/products/catalog/',
            '/websites/domains/',
            '/websites/form/',
            '/websites/contact-us',
            '/time-slot',
            '/locations',
            '/appointment',
            '/work-hour',
            '/events',
            '/notifications/prefix-number',
            '/notifications/send-email-contact',
            '/notifications/get-maintenance',
            '/landing/appointment',
            '/websites/company-logo',
            '/inventories/products/company/abandoned-cart-catalog',
            '/notifications/send-email-verification-token',
            '/client/auth-client/store-verification-token',
            '/client/auth-client/verify-token'
        ];
        foreach ($authorizedRoutes as $route) {
            if (str_contains($request['resource'], $route))
                return self::routeHandler($request);
        }
        Log::info("Error on GatewayHelper-UnauthorizedRoutes");
        abort(Response::HTTP_UNAUTHORIZED, 'Error on GatewayHelper-UnauthorizedRoutes', ['route' => $request['resource']]);
        return [];
    }

    public static function uploadHandler(GateRequest $request)
    {
        try {
            $path = GateEloquent::getPath($request['service']);
            $url = $path->description . $request['resource'];
            $methodName = strtolower($request['method']);
            $headers = ['user-id' => $request['user_id'], 'company-id' => $request['company_id'], 'Accept' => 'application/json'];
            $serviceToken = $path->token;

            switch ($request['resource']) {
                case self::RESOURCE_UPLOAD_FILE:
                case self::RESOURCE_UPLOAD_FILE_UPDATE:
                case self::RESOURCE_UPLOAD_VOUCHER:
                    $body = [
                        'company_id' => $request->get('company_id'),
                        'folder' => $request->get('folder'),
                        'service' => $request->get('service'),
                        'data' => $request->get('data', []),
                        'type' => $request->get('type', ''),
                        'bucket_detail_id' => $request->get('bucket_detail_id', '')
                    ];
                    break;
                case self::RESOURCE_UPLOAD_IMAGES:
                    $body = [];
                    break;
                case self::RESOURCE_UPLOAD_REJECT_SUPPORT:
                    $body = [
                        'data' => $request->get('data', [])
                    ];
                    break;
                case self::RESOURCE_UPLOAD_INSTALLMENT_ATTACHMENT:
                    $data = $request->get('data');
                    $body = [
                        'company_id' => $request->get('company_id'),
                        'folder' => $request->get('folder'),
                        'service' => $request->get('service'),
                        'id' => $data['id'],
                        'payment_method_id' => $data['payment_method_id'],
                        'date' => $data['date'],
                        'value' => $data['value']
                    ];
                    break;
                case self::RESOURCE_UPLOAD_CERTIFICATE:
                    $data = $request->get('data');
                    $body = [
                        'configuration[company_id]' => $request->get('company_id'),
                        'configuration[document_number]' => $data['document_number'],
                        'configuration[email]' => $data['email'],
                        'configuration[social_reason]' => $data['social_reason'],
                        'configuration[signer_role]' => $data['signer_role'],
                        'configuration[password_certificate]' => $data['password_certificate'] ?? null,
                        'configuration[certificate_policy_acceptation_date]' => $data['certificate_policy_acceptation_date'] ?? null,
                        'configuration[test_set_id]' => $data['test_set_id'],
                        'billing_agent[identifier_organization]' => $data['identifier_organization'],
                        'billing_agent[identifier_type]' => $data['identifier_type'],
                        'billing_agent[economic_activity]' => $data['economic_activity'],
                        'billing_agent[city_code]' => $data['city_code'],
                        'billing_agent[city_name]' => $data['city_name'],
                        'billing_agent[postal_code]' => $data['postal_code'],
                        'billing_agent[state_name]' => $data['state_name'],
                        'billing_agent[state_code]' => $data['state_code'],
                        'billing_agent[address_line]' => $data['address_line'],
                        'billing_agent[company_name]' => $data['social_reason'],
                        'billing_agent[nit]' => $data['document_number'],
                        'billing_agent[responsibilities]' => $data['responsibilities'],
                        'billing_agent[tax_id]' => $data['tax_id'],
                        'billing_agent[tax_name]' => $data['tax_name'],
                        'billing_agent[telephone]' => $data['telephone'],
                        'billing_agent[email]' => $data['email']
                    ];

                    break;
                case self::RESOURCE_SEND_ELECTRONIC_DOCUMENT_CUSTOMER:
                    $data = $request->get('data');
                    $body = [
                        'client_email' => $data['client_email'] ?? '',
                        'name_user' => $data['name_user'] ?? '',
                        'subject' => $data['subject'] ?? '',
                        'body_content' => $data['body_content'] ?? '',
                        'logo_url' => $data['logo_url'] ?? '',
                        'company_name' => $data['company_name'] ?? '',
                        'invoice_id' => $data['invoice_id'] ?? '',
                        'invoice_pdf_url' => $data['invoice_pdf_url'] ?? '',
                        'invoice_type' => $data['invoice_type'] ?? ''
                    ];
                    break;
                case self::RESOURCE_SEND_ACCEPTATION_RESPONSE:
                    $data = json_decode($request->get('data'), true);
                    $body = [
                        'company_name' => $data['company_name'] ?? '',
                        'content_logo' => $data['content_logo'] ?? '',
                        'channel' => $data['channel'] ?? '',
                        'customer_email' => $data['customer_email'] ?? '',
                        'supplier_company_name' => $data['supplier_company_name'] ?? '',
                        'document_id' => $data['document_id'] ?? '',
                        'document_name' => $data['document_name'] ?? '',
                        'document_number' => $data['document_number'] ?? '',
                        'file_name' => $data['file_name'] ?? '',
                        'customer_name' => $data['customer_name'] ?? '',
                        "client_email" => $data['client_email'] ?? '',
                        "name_user" => $data['name_user'] ?? '',
                        "invoice_id" => $data['invoice_id'] ?? '',
                        "invoice_pdf_url" => $data['invoice_pdf_url'] ?? '',
                        "subject" => $data['subject'] ?? '',
                        "body_content" => $request->get('body_content') ?? '',
                    ];
                    break;
                case self::RESOURCE_UPLOAD_NOTE_SUPPORT:
                    $data = $request->get('data');
                    $body = [
                        'company_id' => $request->get('company_id'),
                        'folder' => $request->get('folder'),
                        'service' => $request->get('service'),
                        'invoice_id' => $data['invoice_id'],
                    ];
                    break;
                case self::RESOURCE_SEND_EMAIL:
                    $data = json_decode($request->get('data'), true);
                    $body = [
                        'client_email' => $data['client_email'] ?? '',
                        'name_user' => $data['name_user'] ?? '',
                        'subject' => $data['subject'] ?? '',
                        'body_content' => $data['body_content'] ?? '',
                        'logo_url' => $data['logo_url'] ?? '',
                        'company_name' => $data['company_name'] ?? '',
                        'invoice_id' => $data['invoice_id'] ?? '',
                        'invoice_pdf_url' => $data['invoice_pdf_url'] ?? '',
                        'invoice_type' => $data['invoice_type'] ?? ''
                    ];
                    break;
                case self::RESOURCE_BILLS_SEND_EMAIL:
                    $data = $request->get('data');
                    $body = [
                        'id' => $data['id'],
                        'subject' => $data['subject'],
                        'client_email' => $data['client_email'],
                        'body_content' => $data['body_content'],
                        'invoice_type' => $data['invoice_type']
                    ];
                    break;
                case self::RESOURCE_HELP_CENTER_SEND_EMAIL:
                    $data = json_decode($request->get('data'), true);
                    $body = [
                        'subject' => $data['subject'],
                        'type' => $data['type'],
                        'email' => $data['email'],
                        'body' => json_encode($data['body'])
                    ];
                    break;
                case self::RESOURCE_BINNACLE_HELP_CENTER_SEND_EMAIL:
                    $body =  $request->get('data', []);
                    break;
                default:
                    abort(Response::HTTP_UNAUTHORIZED);
                    return [];
            }
            // If no receives file, we send request without this
            if (!$request->has('file')) {
                return Http::withToken($serviceToken)
                ->withHeaders($headers)->$methodName($url, TransformArrayHelper::transformBracketNotationToMultidimensional($body))->json();
            }
            // If you receive a file, you send the request with this
            return Http::withToken($serviceToken)
                ->withHeaders($headers)->attach(
                    'file', $request->file('file')->get(), $request->file('file')->getClientOriginalName()
                )->$methodName($url, $body)->json();

        } catch (\Exception $exception) {
            Log::info("Error on GatewayHelper-upload: " . $exception->getMessage());
            abort(Response::HTTP_BAD_REQUEST, 'Error on Gateway-upload');
            return [];
        }
    }

    public static function uploadManyFiles(GateRequest $request, array $files)
    {
        $path = GateEloquent::getPath($request['service']);
        $url = $path->description . $request['resource'];
        $serviceToken = $path->token;

        $client = Http::withToken($serviceToken);

        collect($files)->each(function (UploadedFile $value, $key) use ($client) {
            $client->attach(
                $key,
                $value->get(),
                $value->getClientOriginalName(),
            );
        });

        switch ($request['method']) {
            case 'POST':
                return $client->withHeaders([
                    'user-id' => $request['user_id'],
                    'company-id' => $request['company_id']
                ])
                    ->post($url, $request->get('data',[]))->json();
            case 'PUT':
                return $client->withHeaders([
                    'user-id' => $request['user_id'],
                    'company-id' => $request['company_id']
                ])
                    ->put($url, $request->get('data',[]))->json();
            default:
                abort(Response::HTTP_UNAUTHORIZED);
                return [];
        }
    }

    /**
     * @param Client $client
     * @param $request
     * @param $token
     * @return StreamedResponse
     * @throws GuzzleException
     */
    private static function handleDownloadFile(Client $client, $request, $token): StreamedResponse
    {
        $fileNameExtension = array_key_exists('file_type', $request['data']) ? $request['data']['file_type'] : 'pdf';
        $response = $client->request($request['method'], $request['resource'],
            [
                'json' => $request['data'],
                'headers' => [
                    'user-id' => $request['user_id'],
                    'company-id' => $request['company_id'],
                    'Authorization' => "Bearer {$token}"
                ]
            ]);
        if (array_key_exists('module', $request['data'])) {
            $fileName = $request['data']['module'] . strtotime(now()) . "." . $request['data']['type'];
        } else {
            $fileName = 'document.'.$fileNameExtension;
        }

        Storage::disk('temp')->put($fileName, (string)$response->getBody());
        return Storage::download("tmp/" . $fileName, $fileName);
    }
}
