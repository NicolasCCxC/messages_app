<?php

namespace App\Http\Controllers;

use App\Http\Requests\Prefix\GetPrefixRequest;
use App\Http\Requests\Prefix\GetSynchronize;
use App\Http\Requests\Prefix\StoreNotesRequest;
use App\Http\Requests\Prefix\StoreRequest;
use App\Infrastructure\Persistence\PrefixEloquent;
use App\Helpers\ExtractJwtJsonHelper;
use App\Http\Requests\Prefix\PrefixTypeRequest;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrefixController extends Controller
{
    use ResponseApiTrait;

    private $prefixEloquent;

    public function __construct(PrefixEloquent $prefixEloquent)
    {
        $this->prefixEloquent = $prefixEloquent;
    }

    public function store(StoreRequest $request)
    {
        return $this->successResponse(
            $this->prefixEloquent->store($request->all(), $request->ip()),
            Module::SECURITY
        );
    }

    public function storeNotes(StoreNotesRequest $request)
    {
        return $this->successResponse(
            $this->prefixEloquent->storeNotes($request->all(), $request->ip()),
            Module::SECURITY
        );
    }

        /**
     * get invoices available
     *
     * @param string $company - company uuid
     * @param Request $request - request information necesary
     * @return JsonResponse
     */
    public function getTypePrefix(string $companyId, Request $request)
    {
        return $this->successResponse(
            $this->prefixEloquent->getPrefix($companyId, $request->all()),
            Module::SECURITY
        );
    }

    public function getSpecificPrefix(GetPrefixRequest $request)
    {
        return $this->successResponse(
            $this->prefixEloquent->getSpecificPrefix($request->all()),
            Module::SECURITY
        );
    }

    /**
     * @param $request
     *
     * @return successResponse
     */
    public function deletePrefixes(Request $request)
    {
        return $this->successResponse(
            $this->prefixEloquent->deletePrefixes($request->all(), $request->ip()),
            Module::SECURITY
        );
    }

    /**
     * get Synchronize prefix
     *
     * @param GetSynchronize $request - request information necesary
     * @return JsonResponse
     */
    public function getSynchronize(GetSynchronize $request):JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->prefixEloquent->getSynchronize($request->all(), $payload["company_id"]),
            Module::SECURITY
        );
    }

    public function rankDepletionPrefix(Request $request)
    {
        return $this->successResponse(
            $this->prefixEloquent->rankDepletionPrefix($request->all()),
            Module::SECURITY
        );
    }

    /**
     * get or create purchase supplier prefixes
     *
     * @param Request $request
     *
     * @return successResponse
     */
    public function getPrefixPurchase(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->prefixEloquent->getPrefixPurchase($payload["company_id"], $request->all()),
            Module::SECURITY
        );
    }

    /**
     *
     * @param PrefixTypeRequest $request
     * @return JsonResponse
     */
    public function setResolutionType(PrefixTypeRequest $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->prefixEloquent->setResolutionType($payload["company_id"], $request->all()),
            Module::SECURITY
        );
    }
}
