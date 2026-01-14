<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyDevice\DeleteRequest;
use App\Http\Requests\CompanyDevice\StoreRequest;
use App\Infrastructure\Persistence\CompanyDeviceEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\ExtractJwtJsonHelper;
use Illuminate\Http\Request;

class CompanyDeviceController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var CompanyDeviceEloquent
     */
    private $companyDeviceEloquent;

    public function __construct(CompanyDeviceEloquent $companyDeviceEloquent)
    {
        $this->companyDeviceEloquent = $companyDeviceEloquent;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyDeviceEloquent->store($request->all()),
            Module::SECURITY,
            'Created resource',
            Response::HTTP_CREATED
        );
    }

    /**
     * Store a newly delete resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyDeviceEloquent->delete($request->all()),
            Module::SECURITY,
            'Delete resource',
            Response::HTTP_OK
        );
    }

    /**
     * Get all device by company id
     *
     * @param Request $request - request information necesary
     * @return JsonResponse
     */
    public function getByCompany(Request $request): JsonResponse
    {
        $payload = ExtractJwtJsonHelper::getJwtInformation($request);
        return $this->successResponse(
            $this->companyDeviceEloquent->getByCompany($payload["company_id"]),
            Module::SECURITY,
            'Delete resource',
            Response::HTTP_OK
        );
    }
}
