<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\DeleteClientRequest;
use App\Http\Requests\Client\InitialDataRequest;
use App\Http\Requests\Client\LastLoginClientRequest;
use App\Http\Requests\Client\StoreBillingClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Infrastructure\Persistence\ClientEloquent;
use App\Infrastructure\Persistence\InitialDataEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Barryvdh\Reflection\DocBlock\Type\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpParser\ErrorHandler\Collecting;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ExtractJwtJsonHelper;

class ClientController extends Controller
{

    use ResponseApiTrait;

    /**
     * @var ClientEloquent
     */
    private $clientEloquent;

    /**
     * @var InitialDataEloquent
     */
    private $initialDataEloquent;


    /**
     * @var Request
     */
    private $request;

    public function __construct(
        ClientEloquent $clientEloquent,
        InitialDataEloquent $initialDataEloquent,
        Request $request
    ) {
        $this->clientEloquent = $clientEloquent;
        $this->request = $request;
        $this->initialDataEloquent = $initialDataEloquent;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->clientEloquent->getAllCompanyClients($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClientRequest $request
     * @return JsonResponse
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        return $this->clientEloquent->storeClient($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return $this->successResponse(
            $this->clientEloquent->getClientById($id),
            Module::SECURITY
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request): JsonResponse
    {
        return $this->clientEloquent->updateClient($request->all());
        //        return $this->successResponse(
        //            $this->clientEloquent->updateClient($request->all()),
        //            Module::SECURITY
        //        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteClientRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(DeleteClientRequest $request, $id): JsonResponse
    {
        return $this->successResponse(
            $this->clientEloquent->clientSoftDelete($request->all(), $id),
            Module::SECURITY
        );
    }

    public function storeBilling(StoreBillingClientRequest $request)
    {
        return $this->successResponse(
            $this->clientEloquent->storeBilling($request->all()),
            Module::SECURITY
        );
    }

    public function showClient(LastLoginClientRequest $request)
    {
        return $this->successResponse(
            $this->clientEloquent->showClient($request->all()),
            Module::SECURITY
        );
    }

    /**
     * Search client by document type
     *
     * @param string $documentId - is the document number
     * @param string $companyId - uuid company
     * @return JsonResponse
     */
    public function getClientByDocument(string $documentId, string $companyId): JsonResponse
    {
        return $this->successResponse(
            $this->clientEloquent->getClientByDocument($documentId, $companyId),
            Module::SECURITY
        );
    }

    public function initialData(InitialDataRequest $request)
    {
        return $this->successResponse(
            $this->initialDataEloquent->setUp($request->all()),
            Module::SECURITY,
        );
    }

    public function createOrGetFinalCustomer(string $companyId)
    {
        return $this->successResponse(
            $this->clientEloquent->createOrGetFinalCustomer($companyId),
            Module::SECURITY,
        );
    }
}
