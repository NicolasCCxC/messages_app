<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use \Illuminate\Http\Request;

class MembreshipModulesResource extends JsonResource
{

    protected $modules;

    /**
     * Add external data to the resource
     *
     * @param Collection $value
     * @return $this
     */
    public function using(Collection $value)
    {
        $this->modules =  $value;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => isset($this->modules) ? $this->modules->firstWhere('id', $this->membership_modules_id)['name'] : 'Not found',
            'created_at' => Carbon::parse($this->created_at)->getTimestamp(),
            'updated_at' => Carbon::parse($this->updated_at)->getTimestamp(),
            'sub_modules' => SubModulesResource::make($this),
        ];
    }
}
