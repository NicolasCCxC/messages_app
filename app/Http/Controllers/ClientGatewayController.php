<?php

namespace App\Http\Controllers;

use App\Http\Requests\GateRequest;
use App\Infrastructure\Formulation\GatewayHelper;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientGatewayController extends Controller
{
    use ResponseApiTrait;


    /**
     * @param GateRequest $request
     * @return array|mixed|StreamedResponse
     * @throws GuzzleException
     */
    public function gate(GateRequest $request)
    {
        return GatewayHelper::routeHandler($request);
    }

    public function upload(GateRequest $request)
    {
        return GatewayHelper::uploadHandler($request);
    }

    /**
     * @param GateRequest $request
     * @return array|mixed|StreamedResponse
     * @throws GuzzleException
     */
    public function lock(GateRequest $request)
    {
        return GatewayHelper::authorizedRoutes($request);
    }
}
