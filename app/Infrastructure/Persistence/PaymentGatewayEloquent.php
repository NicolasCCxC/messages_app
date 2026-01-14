<?php

namespace App\Infrastructure\Persistence;

use App\Models\PaymentGateway;

class PaymentGatewayEloquent
{
    private $paymentGatewayModel;


    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGatewayModel = $paymentGateway;
    }

    public function getAll(){
        return $this->paymentGatewayModel::all();
    }

}
