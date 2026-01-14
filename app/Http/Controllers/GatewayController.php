<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gateway\CashTransferRequest;
use App\Http\Requests\Gateway\CreditCardTransferRequest;
use App\Http\Requests\Gateway\PSETransferRequest;
use App\Infrastructure\Gateway\HandlePayment;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\Request;

class GatewayController extends Controller
{


    private $handlePayment;

    public function __construct(HandlePayment $handlePayment)
    {
        $this->handlePayment = $handlePayment;
    }

    public function methods(Request $request, string $id)
    {
        return $this->successResponse(
            $this->handlePayment->methods($id, $request->header('company-id'))
        );
    }

    public function pse(Request $request, string $id)
    {
        return $this->successResponse(
            $this->handlePayment->pse($id, $request->header('company-id'))
        );
    }

    public function pseTransfer(PSETransferRequest $request, string $id)
    {
        return $this->successResponse(
            $this->handlePayment->pseTransfer($id, $request->all(), $request->header('company-id'), $request->header('user-id'))
        );
    }

    public function creditCardTransfer(CreditCardTransferRequest $request, string $id)
    {
        return $this->successResponse(
            $this->handlePayment->creditCardTransfer($id, $request->all(), $request->header('company-id'), $request->header('user-id'))
        );
    }

    public function report(Request $request,string $id, string $transactionId)
    {
        return $this->successResponse(
            $this->handlePayment->report($id, $transactionId ,$request->header('company-id'), $request->header('user-id'))
        );
    }

    public function cashTransfer(CashTransferRequest $request, string $id)
    {
        return $this->successResponse(
            $this->handlePayment->cashTransfer($id, $request->all(), $request->header('company-id'), $request->header('user-id'))
        );
    }
}
