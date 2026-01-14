<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyInformation\StoreRequest;
use App\Infrastructure\Persistence\CompanyInformationEloquent;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyInformationController extends Controller
{

    private $companyInformationEloquent;

    public function __construct(CompanyInformationEloquent $companyInformationEloquent)
    {
        $this->companyInformationEloquent = $companyInformationEloquent;
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyInformationEloquent->store($request->all())
        );
    }

    public function get(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyInformationEloquent->getByCompany($request->header('company-id'))
        );
    }

}
