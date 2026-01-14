<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StoreRequest;
use App\Infrastructure\Persistence\PermissionEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use ResponseApiTrait;

    /**
     * @var PermissionEloquent
     */
    private $permissionEloquent;

    /**
     * Class constructor
     *
     * @param PermissionEloquent $permissionEloquent
     */
    public function __construct(PermissionEloquent $permissionEloquent)
    {
        $this->permissionEloquent = $permissionEloquent;
    }

    /**
     * show all permission
     *
     * @return JsonResponse
     */
    public function index () : JsonResponse
    {
        return $this->successResponse(
            $this->permissionEloquent->getAllPermission(),
            Module::SECURITY
        );
    }

    /**
     * Add new permission
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store (StoreRequest $request) : JsonResponse
    {

        return $this->successResponse(
            $this->permissionEloquent->storePermission($request->all()),
            Module::SECURITY,
            'Success operation',
            Response::HTTP_CREATED
        );
    }

    public function formatPermissions () : JsonResponse
    {
        return $this->successResponse(
            $this->permissionEloquent->formatPermisisons(),
            Module::SECURITY,
            'Success operation',
            Response::HTTP_ACCEPTED
        );
    }
}
