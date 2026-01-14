<?php

namespace App\Infrastructure\Gateway;

use App\Enums\PayU;
use App\Infrastructure\Interfaces\IPaymentGateway;

class GatewayFactory
{
    /**
     * This function allow create a specific object
     *
     * @param int $idGatewayPayment
     * @return PayUGateway|null
     */
    public static function createAGateway(int $idGatewayPayment,array $keys) : IPaymentGateway
    {
        switch ($idGatewayPayment){
            case PayU::ID_GATEWAY:
                $factory = new PayUGateway($keys);
                break;
            default:
                $factory = null;
        }

        return $factory;
    }
}
