<?php

namespace App\Http\Controllers;

use App\Infrastructure\Persistence\PaymentGatewayEloquent;
use App\Traits\ResponseApiTrait;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{

    private $paymentGatewayEloquent;

    public function __construct(PaymentGatewayEloquent $paymentGatewayEloquent)
    {
        $this->paymentGatewayEloquent = $paymentGatewayEloquent;
    }

    public function index()
    {
        return $this->successResponse(
            $this->paymentGatewayEloquent->getAll()
        );
    }
}
