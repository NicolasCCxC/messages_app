<?php

namespace App\Infrastructure\Persistence;

use App\Enums\EmailTokenVerification;
use App\Http\Resources\ClientResource;
use App\Http\Resources\Login\EmailVerificationTokenResource;
use App\Infrastructure\Formulation\UserHelper;
use App\Infrastructure\Formulation\InvoiceHelper;
use App\Infrastructure\Services\InvoiceService;
use App\Models\Client;
use App\Models\EmailVerificationToken;
use App\Models\Module;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use App\Traits\ResponseApiTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ClientEloquent
{
    use ResponseApiTrait;

    private $model;
    private $data;
    private $invoiceService;

    function __construct()
    {
        $this->model = new Client();
        $this->invoiceService = new InvoiceService();
    }

    /**
     * @param string $idCompany
     *
     * @return AnonymousResourceCollection
     */
    public function getAllCompanyClients(string $idCompany): AnonymousResourceCollection
    {
        return ClientResource::collection(
            Client::with([
                'companies' => function ($companies) use ($idCompany) {
                    $companies->where('id', $idCompany);
                }
            ])->orderBy('created_at', 'asc')->get()
        );
    }

    /**
     * Store a new user
     * @param array $clientData
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeClient(array $clientData)
    {
        $clientData['password'] = Hash::make($clientData['password']);

        $responseClient = $this->model::where('email', $clientData['email'])->whereRelation('companies', 'company_id', $clientData['company_id'])->first();

        if ($responseClient && !isset($responseClient->password)) {
            return $this->successResponse(
                new ClientResource($responseClient),
                Module::SECURITY,
                EmailTokenVerification::TOKEN_REQUIRED,
                Response::HTTP_OK
            );
        }
        //create user
        $save = [
            'email' => $clientData['email'],
            'password' => $clientData['password'],
            'password_confirmation' => $clientData['password_confirmation'],
            'name' => $clientData['name']
        ];
        if (!$responseClient) {
            $client = $this->model->query()->create($save);
            $client->companies()->attach(['id' => $clientData['company_id']]);

            InvoiceHelper::createClientInvoice([
                'name' => $client['name'] ?? '',
                'client_id' => $client['id'],
                'email' => $client['email']
            ], $clientData['company_id'], $client->id);

            return $this->successResponse(
                new ClientResource($client),
                Module::SECURITY,
                "Created resource",
                Response::HTTP_CREATED
            );
        } else {
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_CONFLICT,
                'Email used for other client in the same company'
            );
        }
    }

    public function assignCompanyAndSuperAdmin(string $idClient, string $idCompany)
    {
        $client = $this->model::findOrFail($idClient);
        $client->company_id = $idCompany;
        $client->save();
        UserHelper::assignSuperAdminRole($client->id);
    }

    /**
     * Get a user by id
     * @param string $id
     * @return ClientResource
     */
    function getClientById(string $id)
    {
        return ClientResource::make(Client::query()->findOrFail($id));
    }

    /**
     * Update a user
     *
     * @param array $userUpdateFields
     * @return mixed
     */
    public function updateClient(array $userUpdateFields)
    {
        $update = [
            'id' => $userUpdateFields['id'],
            'email' => $userUpdateFields['email'],
            'name' => $userUpdateFields['name'],
            'company_id' => $userUpdateFields['company_id']
        ];

        $client = Client::query()->findOrFail($userUpdateFields['id']);

        if ($client) {

            $client->fill($update);

            if (array_key_exists('password', $userUpdateFields)) {
                $client['password'] = Hash::make($userUpdateFields['password']);
            }
            if (!$client->companies()->find($userUpdateFields['company_id'])) {
                $client->companies()->attach(['id' => $userUpdateFields['company_id']]);
            }
            $response = $this->model::where('email', $userUpdateFields['email'])->where('id', '!=', $userUpdateFields['id'])->whereRelation('companies', 'company_id', $userUpdateFields['company_id'])->first();
            if (!$response) {
                return $this->successResponse(
                    $client->save(),
                    Module::SECURITY
                );
            } else {
                return $this->errorResponse(
                    Module::SECURITY,
                    Response::HTTP_CONFLICT,
                    'Email used for other client in the same company'
                );
            }
        } else {
            return $this->errorResponse(
                Module::SECURITY,
                Response::HTTP_NOT_MODIFIED,
                'client not exist'
            );
        }
    }

    /**
     *
     *
     * @param array $data
     * @param string $idCompany
     * @return AnonymousResourceCollection
     */
    public function clientSoftDelete(array $data, string $idCompany): AnonymousResourceCollection
    {
        foreach ($data as $item) {
            $user = Client::query()->findOrFail($item['id']);
            $user->delete();
        }
        return $this->getAllCompanyClients($idCompany);
    }

    /**
     * Store a new user from billing
     * @param array $clientData
     * @return ClientResource
     */
    public function storeBilling(array $clientData)
    {
        $dataClient = $this->model::where('email', $clientData['email'])->whereRelation('companies', 'company_id', $clientData['company_id'])->first();

        if (!$clientData['isPurchaseOrder']) {
            $dataClientDocument = $this->model::where('document_number', $clientData['document_number'])->whereRelation('companies', 'company_id', $clientData['company_id'])->first();
            if (isset($dataClientDocument))
                $dataClient = $dataClientDocument;
        }

        if (isset($dataClient)) {
            //update user
            $client = Client::query()->findOrFail($dataClient['id']);
            $client->fill($clientData);
            $client->companies()->attach(['company-id' => $clientData['company_id']]);
            $client->save();
            return new ClientResource($client);
        } else {
            //create user
            $client = $this->model->query()->create($clientData);
            $client->companies()->attach(['id' => $clientData['company_id']]);
            return new ClientResource($client);
        }
    }

    public function attemptLogin(array $data)
    {
        return (bool) $this->model::where('email', $data['email'])
            ->whereHas('companies', function ($subQuery) use ($data) {
                $subQuery->where('id', $data['company_id']);
            })->first();
    }

    public function showClient(array $request)
    {
        return collect($request)->map(function ($value) {
            $lastLogin = $this->model::whereHas('companies', function ($query) use ($value) {
                return $query->where('id', $value['company_id']);
            })->where('id', $value['client_id'])->select('last_login')->first();
            return [
                'client_id' => $value['client_id'],
                'company_id' => $value['company_id'],
                'last_login' => $lastLogin['last_login']
            ];
        });
    }

    /**
     * Retrieves a client by document number and company ID.
     *
     * @param string $documentId The document number of the client.
     * @param string $companyId The ID of the company.
     * @return \Illuminate\Database\Eloquent\Model|null The client matching the document number and company ID, or null if not found.
     */

    public function getClientByDocument(string $documentId, string $companyId)
    {
        return $this->model::where('document_number', $documentId)
            ->whereRelation('companies', 'company_id', $companyId)
            ->first();
    }

    /**
     * Retrieves the final consumer for a given company. If multiple entries
     * exist for the final consumer in the companies_clients table, duplicates
     * are removed and a single entry is reinserted. If no entry exists, a new
     * client is created and associated with the company.
     *
     * @param string $companyId The ID of the company to retrieve the final consumer for.
     * @return string The ID of the final consumer client.
     */

    public function createOrGetFinalCustomer(string $companyId)
    {
        // The final consumer of this company is consulted, as well as the records in the companies_client table.
        $finalConsumer = $this->getClientFinalConsumerByCompany($companyId);

        // If there is a case where you have more than one record in companies_clients
        if (count($finalConsumer) > 1) {
            // We check for duplicate records Browse duplicates
            DB::table('companies_clients')
                ->where('client_id', $finalConsumer->first()->client_id)
                ->where('company_id', $finalConsumer->first()->company_id)
                ->delete();

            DB::table('companies_clients')->insert([
                'client_id' => $finalConsumer->first()->client_id,
                'company_id' => $finalConsumer->first()->company_id,
            ]);

            return $this->getClientFinalConsumerByCompany($companyId)->first()['id'];
        } else if (count($finalConsumer) == 1) {
            // Returns the client_id, to finish the creation process

            return $finalConsumer->first()['id'];
        } else {
            // We create the client with the relationship to the company
            $client = $this->model->query()->create([
                'document_number' => Client::DEFAULT_DOCUMENT_NUMBER_FINAL_CUSTOMER,
                'document_type' => Client::DEFAULT_DOCUMENT_TYPE_FINAL_CUSTOMER,
                'name' => 'Consumidor Final',
            ]);
            $client->companies()->attach(['id' => $companyId]);

            return $client['id'];
        }
    }

    /**
     * Gets the final consumer of a company. This is a client with the document number 222222222222.
     * The final consumer is a special client that is used to store the data of the company's final consumer.
     * This client is used to store the data of the final consumer of the company,
     * such as the name, document number, and document type.
     *
     * @param string $companyId The id of the company.
     * @return \Illuminate\Database\Eloquent\Collection|\App\Infrastructure\Models\CompanyClient[]
     */
    public function getClientFinalConsumerByCompany(string $companyId)
    {
        return $this->model::join('companies_clients', 'clients.id', '=', 'companies_clients.client_id')
            ->where('companies_clients.company_id', $companyId)
            ->where('clients.document_number', Client::DEFAULT_DOCUMENT_NUMBER_FINAL_CUSTOMER)
            ->get();
    }


    /**
     * Stores a record to email_verification_token table
     *
     * @param array $request The data containing email and token for saving data
     */
    public function storeVerificationToken(array $request): void
    {
        EmailVerificationToken::where('email', $request['email'])->delete();

        EmailVerificationToken::create([
            'email' => $request['email'],
            'token' => Hash::make($request['token']),
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * Receives a token and verifies it
     *
     * @param array $request The data containing email and token for token verification
     * @return array The data with the processing information
     */
    public function verifyToken(array $request): array
    {
        $record = EmailVerificationToken::where('email', $request['email'])->first();
        if (!$record) {
            return ['status' => false, 'message' => EmailTokenVerification::TOKEN_NOT_FOUND];
        }
        if ($record->isExpired()) {
            return ['status' => false, 'message' => EmailTokenVerification::TOKEN_EXPIRED];
        }
        if (!Hash::check($request['token'], $record->token)) {
            return ['status' => false, 'message' => EmailTokenVerification::INVALID_TOKEN];
        }

        $request['password'] = Hash::make($request['password']);
        $responseClient = $this->model::where('email', $request['email'])->whereRelation('companies', 'company_id', $request['company_id'])->first();
        $responseClient->password = $request['password'];
        $responseClient->save();

        return ['status' => true, 'message' => EmailTokenVerification::VALID_TOKEN];
    }
}
