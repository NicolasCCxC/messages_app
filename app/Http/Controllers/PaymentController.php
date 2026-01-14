<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\PaymentEloquent;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    private $paymentEloquent;

    public function __construct(PaymentEloquent $paymentEloquent)
    {
        $this->paymentEloquent = $paymentEloquent;
    }

    public function clientIndex($clientId)
    {
        return $this->successResponse(
            $this->paymentEloquent->clientIndex($clientId)
        );
    }
}
