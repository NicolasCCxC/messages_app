<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhysicalStore\PhysicalStoreRequest;
use App\Infrastructure\Persistence\PhysicalStoreEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ExtractJwtJsonHelper;

class PhysicalStoreController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var PhysicalStoreEloquent
     */
    private $physicalStoreEloquent;

    /**
     * @var Request
     */
    private $request;

    public function __construct(PhysicalStoreEloquent $physicalStoreEloquent, Request $request)
    {
        $this->physicalStoreEloquent = $physicalStoreEloquent;
        $this->request = $request;
    }

    /**
     * @param Request $request - request information necesary
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getAllPhysicalStoresByCompany(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->physicalStoreEloquent->getAllPhysicalStoresByCompany($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * @param PhysicalStoreRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function store(PhysicalStoreRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->physicalStoreEloquent->store($request->all()),
            Module::SECURITY
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function delete($id): JsonResponse
    {
        return $this->successResponse(
            $this->physicalStoreEloquent->delete($id),
            Module::SECURITY
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function deletePointSale($id): JsonResponse
    {
        return $this->successResponse(
            $this->physicalStoreEloquent->deletePointSale($id),
            Module::SECURITY
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function deletePhysicalStoreOrPointSaleByIds(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->physicalStoreEloquent->deletePhysicalStoreOrPointSaleByIds($request->all()),
            Module::SECURITY
        );
    }

}
