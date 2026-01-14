<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ModulesCollection extends ResourceCollection
{
    private $modules;

    public function using(array $modules): ModulesCollection
    {
        $this->modules = collect($modules);
        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $response = $this->modules;

        return $response->all();
    }
}
