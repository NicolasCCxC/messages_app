<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyPayment\StoreRequest;
use App\Infrastructure\Persistence\CompanyInformationEloquent;
use App\Infrastructure\Persistence\CompanyPaymentGatewayEloquent;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyPaymentGatewayController extends Controller
{

    private $companyPaymentGatewayELoquent;

    public function __construct(CompanyPaymentGatewayEloquent $companyInformationEloquent)
    {
        $this->companyPaymentGatewayELoquent = $companyInformationEloquent;
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyPaymentGatewayELoquent->store($request->all(), $request->header('company-id')),
        );
    }

    public function getAll(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->companyPaymentGatewayELoquent->getAll($request->header('company-id'))
        );
    }
}
