<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

trait CommunicationBetweenServicesTrait
{

    public function makeRequest(string $method, string $service, string $resource, string $userId, string $companyId, array $data = [], bool $allResponse = false)
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $request = [
            'resource' => $resource,
            'method' => strtoupper($method),
            'service' => strtoupper($service),
            'user_id' => $userId ?? Str::uuid()->toString(),
            'company_id' => $companyId ?? Str::uuid()->toString(),
            'data' => $data
        ];

        if ($request['service'] == 'SECURITY') {
            if (in_array($request['method'], ['GET', 'POST', 'PUT', 'DELETE'])) {
                $method = strtolower($request['method']);
                $response = Http::withToken(env('SERVICE_TOKEN'))
                    ->$method(env('URL_GATEWAY') . $request['resource'],
                        $request
                    );
            }
        } else {
            if ($image ==! null){
                $response = Http::withToken(env('SERVICE_TOKEN'))
                    ->attach('file', $image->get(), $image->getClientOriginalName())
                    ->post(env('URL_GATEWAY') . 'api/gateway',
                        $request
                    );
            }else{
                $response = Http::withToken(env('SERVICE_TOKEN'))
                    ->timeout(50)
                    ->post(env('URL_GATEWAY') . 'api/gateway',
                        $request
                    );
            }
        }

        $badStatus = [
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_NOT_FOUND,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::HTTP_FAILED_DEPENDENCY
        ];

        if (isset($response['statusCode']) && in_array($response['statusCode'], $badStatus)) {
            return $response->json();
        } elseif ($response->json() === null) {
            return [];
        }

        return $allResponse ? $response->json() : $response->json()['data'];
    }

    public function getSecurity(string $url)
    {
        $response = Http::withToken(env('SERVICE_TOKEN'))
            ->get(env('URL_GATEWAY') . 'api/' . $url);

        if (isset($response['statusCode']) && in_array($response['statusCode'], [400, 401, 404, 422])) {
            return $response->json();
        }
        return $response->json()['data'];
    }

    public function postSecurity(string $url, array $data)
    {
        try {
            $response = Http::withToken(env('SERVICE_TOKEN'))
                ->post(env('URL_GATEWAY') . 'api/' . $url, $data);

            if (isset($response['statusCode']) && in_array($response['statusCode'], [Response::HTTP_BAD_REQUEST, Response::HTTP_NOT_FOUND, rESPONSE::HTTP_UNAUTHORIZED, Response::HTTP_UNPROCESSABLE_ENTITY], true)) {
                return $response->json();
            }
            return $response->json()['data'];

        } catch (Exception $e) {
            return [];
        }
    }
}
