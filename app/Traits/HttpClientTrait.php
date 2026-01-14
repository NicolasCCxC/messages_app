<?php

namespace App\Traits;


use Illuminate\Support\Facades\Http;

trait HttpClientTrait
{
    public function makePost(string $url, array $data = [], array $headers = [])
    {
        return Http::withHeaders(array_merge([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],$headers))
            ->post($url, $data)->json();
    }

    public function makeGet(string $url)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->get($url)->json();
    }
}
