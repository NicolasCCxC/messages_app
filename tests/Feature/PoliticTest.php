<?php

namespace App\Http\Controllers;

use App\Http\Requests\Politic\DataPrivacyPolicyRequest;
use App\Http\Requests\Politic\PrivacyPurposesRequest;
use App\Http\Requests\Politic\IndexPoliticsRequets;
use App\Http\Requests\Politic\StorePoliticsRequets;
use App\Infrastructure\Persistence\PoliticEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ExtractJwtJsonHelper;

class PoliticController extends Controller
{
    use ResponseApiTrait;

    private $politicEloquent;

    public function __construct()
    {
        $this->politicEloquent = new PoliticEloquent();
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id ?? $request->get('company_id');
        return $this->successResponse(
            $this->politicEloquent->getAllPolitics($companyId),
            Module::SECURITY
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePoliticsRequets  $request
     * @return JsonResponse
     */
    public function store(StorePoliticsRequets $request): JsonResponse
    {
        return $this->successResponse(
            $this->politicEloquent->storesPolitics($request->all(), $request->ip()),
            Module::SECURITY
        );
    }

    /**
     * Display the specified resource.
     * @return JsonResponse
     */
    public function show(IndexPoliticsRequets $requets): JsonResponse
    {
        return $this->successResponse(
            $this->politicEloquent->getById($requets->all()),
            Module::SECURITY
        );
    }

     /**
     * Update Data Privacy Policy
     *
     * @param DataPrivacyPolicyRequest $request
     * @param string $companyId
     * @return JsonResponse
     */
    public function storeDataPrivacyPolicy(DataPrivacyPolicyRequest $request, string $companyId)
    {
        return $this->successResponse(
            $this->politicEloquent->storeDataPrivacyPolicy($companyId, $request->all()),
            Module::SECURITY
        );
    }

    public function delete(string $id)
    {
        return $this->successResponse(
            $this->politicEloquent->delete($id),
            Module::SECURITY
        );
    }

    /**
     * Get purposes by company.
     *
     * @param  Request $request - request information necesary
     * @return JsonResponse
     */
    public function getPurposeByCompanyId(Request $request):JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->politicEloquent->getPurposeByCompanyId($payload["company_id"]),
            Module::SECURITY
        );
    }

    /**
     * Store or update purposes.
     *
     * @param  PrivacyPurposesRequest  $request
     * @return JsonResponse
     */
    public function storeOrUpdatePrivacyPurpose(PrivacyPurposesRequest $request)
    {
        $companyId = auth()->user()->company_id ?? $request->header('company-id');
        return $this->successResponse(
            $this->politicEloquent->storeOrUpdatePurpose($request->all(), $companyId),
            Module::SECURITY
        );
    }

    /**
     * Delete purpose.
     *
     * @param  string  $purposeId
     * @return JsonResponse
     */
    public function deletePrivacyPurpose(string $purposeId)
    {
        return $this->successResponse(
            $this->politicEloquent->deletePurposeById($purposeId),
            Module::SECURITY
        );
    }

}
