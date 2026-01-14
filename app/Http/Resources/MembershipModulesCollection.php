<?php

namespace App\Http\Resources;

use App\Models\MembershipHasModules;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipModulesCollection extends ResourceCollection
{

    protected $modules;

    /**
     * Add external data to the resource
     *
     * @param $value
     * @return MembershipModulesCollection
     */
    public function using($value)
    {
        $this->modules = $value;
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
        $response = collect();
        if (isset($this->modules)) {
            $response = $this->collection->map(function (MembershipHasModules $module) use ($request) {
                return MembreshipModulesResource::make($module)->using($this->modules)->toArray($request);
            });
        }
    
        $response->push([
            'id' => Str::uuid()->toString(),
            'name' => 'Perfil de la empresa',
            'created_at' => Carbon::now()->getTimestamp(),
            'updated_at' => Carbon::now()->getTimestamp(),
        ]);
        $response->push([
            'id' => Str::uuid()->toString(),
            'name' => 'Reportes analíticos',
            'created_at' => Carbon::now()->getTimestamp(),
            'updated_at' => Carbon::now()->getTimestamp(),
        ]);
        return $response->all();
    }
}
