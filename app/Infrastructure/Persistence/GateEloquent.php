<?php


namespace App\Infrastructure\Persistence;


use App\Models\Module;

class GateEloquent
{

    /**
     * @param $service
     * @return Module
     */
    public static function getPath($service): Module
    {
        return Module::firstWhere('name', '=', $service);
    }

}
