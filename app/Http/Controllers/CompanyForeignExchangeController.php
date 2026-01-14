<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyForeignExchange\StoreRequest;
use App\Http\Requests\CompanyForeignExchange\UpdateRequest;
use App\Infrastructure\Persistence\CompanyForeignExchangeEloquent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use App\Helpers\ExtractJwtJsonHelper;

class CompanyForeignExchangeController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var CompanyForeignExchangeEloquent
     */
    private $CompanyForeignExchangeEloquent;

    public function __construct(CompanyForeignExchangeEloquent $CompanyForeignExchangeEloquent, Request $request)
    {
        $this->CompanyForeignExchangeEloquent = $CompanyForeignExchangeEloquent;
    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->CompanyForeignExchangeEloquent->store(
                $request->all(),
            ),
            Module::SECURITY
        );
    }

     /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getAll(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->CompanyForeignExchangeEloquent->getAll(
                $request->all(), $payload["company_id"]
            ),
            Module::SECURITY
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        return $this->successResponse(
            $this->CompanyForeignExchangeEloquent->update(
                $request->all(), $id
            ),
            Module::SECURITY
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function delete(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->CompanyForeignExchangeEloquent->delete(
                $request->all(), $payload["company_id"]
            ),
            Module::SECURITY
        );
    }
}
