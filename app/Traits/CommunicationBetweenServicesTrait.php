<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait CommunicationBetweenServicesTrait
{
    protected function auth() : array
    {
        $data = array(
            'email' => env('app.USER_AUTH','fgonzalez@ccxc.us'),
            'password' => env('app.PASSWORD_AUTH','@!F+CCxC2@2@+E!@'),
        );

        $response = Http::post(env('app.URL_GATEWAY', 'https://qa-api-security.famiefi.com').'/api/auth/login',$data);

        $token = $response->json()['data']['access_token'];

        $request = array (
            'resource' => '',
            'method' => '',
            'service' => '',
            'user_id' => $response->json()['data']['user']['id'],
            'company_id' => $response->json()['data']['user']['company_id'],
            'data' =>
            array (
            ),
        );

        return [
            'request' => $request,
            'token' => $token
        ];
    }

    public function makeRequest(string $method, string $service, string $resource, array $data = []) : array
    {
        $requestAndToken = $this->auth();
        $request = $requestAndToken['request'];
        $token = $requestAndToken ['token'];

        $request['method'] = strtoupper($method);
        $request['resource'] = $resource;
        $request['service'] = strtoupper($service);
        $request['data'] = $data;

        $response = Http::withToken($token)
            ->post(env('app.URL_GATEWAY', 'https://qa-api-security.famiefi.com').'/api/'.strtolower($service),
            $request
        );

        if($response->json()['statusCode'] != 202)
            return $response->json();

        if(!is_array($response->json()['data']))
           return [$response->json()['data']];

        return $response->json()['data'];
    }
}
