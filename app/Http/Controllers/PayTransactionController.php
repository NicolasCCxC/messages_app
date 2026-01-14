<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayTransaction\UpdateJsonPseRequest;
use App\Infrastructure\Persistence\PayTransactionEloquent;
use App\Models\Module;
use App\Traits\ResponseApiTrait;


class PayTransactionController extends Controller
{

    use ResponseApiTrait;

    protected $payTransactionEloquent;

    public function __construct(PayTransactionEloquent $payTransactionEloquent)
    {
        $this->payTransactionEloquent = $payTransactionEloquent;
    }

    public function getByTransaction(string $transactionId)
    {
        return $this->successResponse(
            $this->payTransactionEloquent->getByTransactionId($transactionId),
            Module::SECURITY
        );
    }

    public function updateJsonPseUrlResponse(UpdateJsonPseRequest $request)
    {
        $updated = $this->payTransactionEloquent->updateJsonPseUrlResponse(
            $request->input('transaction_id'),
            $request->input('json_pse_url_response')
        );

        return $this->successResponse(
            $updated,
            Module::SECURITY,
            'JSON PSE URL Response updated successfully.'
        );
    }
}
