<?php

namespace App\Http\Controllers;

use App\Http\Requests\GateRequest;
use App\Infrastructure\Formulation\GatewayHelper;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Helpers\ExtractJwtJsonHelper;
use Illuminate\Support\Str;
use App\Models\Module;
use App\Models\Company;
use Symfony\Component\HttpFoundation\Response;

class GatewayController extends Controller
{
    use ResponseApiTrait;

    /**
     * @param GateRequest $request
     * @return array|mixed|StreamedResponse
     * @throws GuzzleException
     */
    public function gate(GateRequest $request)
    {
        $token = $request->bearerToken();
        $is_a_service = Module::where('token', $token)->exists();
        if (!$is_a_service) {
            $payload = ExtractJwtJsonHelper::getJwtInformation($request);
            if ($request['company_id'] != $payload['company_id'] || $request['user_id'] != $payload['user_id']) {
                return $this->errorResponse(
                    Module::SECURITY,
                    Response::HTTP_UNAUTHORIZED,
                    'Unauthorized'
                );
            }
            $request->merge(['user_id' => $payload['user_id']]);
            $request->merge(['company_id' => $payload['company_id']]);
            if (isset($request['resource'])) {
 
                if (preg_match("/^[^\/]/", $request['resource']) || preg_match("/\/{2,}|\/$/", $request['resource'])) {
                    return $this->errorResponse(
                        Module::SECURITY,
                        Response::HTTP_BAD_REQUEST,
                        'Bad Request'
                    );
                }
 
                $parts = explode('/', $request['resource']);
                array_walk($parts, function(&$item) use ($payload)
                {
                    if (is_string($item) && preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $item)) {
                        if(Company::where('id', $item)->exists())
                            $item = $payload['company_id'];
                    } 
                } );
                $request['resource'] = implode('/', $parts);
            }
        }
        return GatewayHelper::routeHandler($request);
    }
    
    public function upload(GateRequest $request)
    {
        return GatewayHelper::uploadHandler($request);
    }

    public function uploadManyFiles(GateRequest $request)
    {
        return GatewayHelper::uploadManyFiles($request, $request->file());
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
