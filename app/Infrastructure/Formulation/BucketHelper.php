<?php

namespace App\Infrastructure\Formulation;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BucketHelper
{
    /**
     * @throws GuzzleException
     */
    public static function getUrl($id)
    {
        return GatewayHelper::routeHandler([
                'resource' => '/bucket/bucket-detail/' . $id,
                'method' => 'GET',
                'service' => 'BUCKET',
                'data' => [],
                'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
                'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
            ])['data'] ?? [];
    }

    /**
     * @throws GuzzleException
     */
    public static function getList($data)
    {
        return GatewayHelper::routeHandler([
                'resource' => '/bucket/list/',
                'method' => 'POST',
                'service' => 'BUCKET',
                'data' => $data,
                'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
                'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
            ])['data'] ?? [];
    }

    /**
     * @throws GuzzleException
     */
    public static function deleteBucketDetail($id, array $data = [])
    {
        return GatewayHelper::routeHandler([
                'resource' => '/bucket/bucket-detail/' . $id,
                'method' => 'DELETE',
                'service' => 'BUCKET',
                'data' => $data,
                'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
                'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
            ])['data'] ?? [];
    }

    /**
     * @param array $data
     *
     * @return array|mixed
     *
     * @throws GuzzleException
     */
    public static function getElectronicInvoicePreview(array $data = [])
    {
        $response = GatewayHelper::routeHandler([
                'resource' => '/document/electronic-invoice/invoice-preview',
                'method' => 'POST',
                'service' => 'BUCKET',
                'data' => [
                    'type' => 'pdf',
                    'module' => 'electronic-invoice-preview',
                    'folder' => 'electronic-document',
                    'data' => $data
                ],
                'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
                'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
            ]);
        Log::info('Response preview', [$response]);
        return $response['data'] ?? [];
    }

        /**
     * @param array $data
     *
     * @return array|mixed
     *
     * @throws GuzzleException
     */
    public static function getBucketDetailByBucketId(string $id)
    {
        return GatewayHelper::routeHandler([
                'resource' => '/bucket/bucket-detail/' . $id,
                'method' => 'GET',
                'service' => 'BUCKET',
                'data' => [],
                'user_id' => auth()->user()->id ?? Str::uuid()->toString(),
                'company_id' => auth()->user()->company_id ?? Str::uuid()->toString(),
            ])['data']['id'] ?? null;
    }
}
