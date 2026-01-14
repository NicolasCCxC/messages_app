<?php

namespace App\Infrastructure\Persistence;

use App\Http\Resources\CompanyDevice\CompanyDeviceResource;
use App\Models\CompanyDevice;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class CompanyDeviceEloquent
{
    private $model;

    function __construct()
    {
        $this->model = new CompanyDevice();
    }
    
    /**
     * Store a new company devices
     * @param array $data
     * @return AnonymousResourceCollection
     */
    public function store(array $data)
    {
        foreach ($data['devices'] as $value) {
            $name = strtoupper($value['name']);
            $getDevices = $this->model->where('name', $name)->where('company_id', $data['company_id'])->first();
            if (isset($getDevices)) {
                $value['id'] = $getDevices->id;
            }
            $this->model::updateOrCreate(
                ['id' => $value['id'] ?? Str::uuid()->toString()],
                [
                    'name' => $name,
                    'company_id' => $data['company_id']
                ]
            );
        }
        return  CompanyDeviceResource::collection($this->model::where('company_id', $data['company_id'])->get());
    }

     /**
     * Delete many company devices
     * @param array $data
     * @return AnonymousResourceCollection
     */
    public function delete(array $data)
    {
        $this->model->whereIn('id', $data['ids'])->delete();
        return CompanyDeviceResource::collection($this->model::where('company_id', $data['company_id'])->get());
    }

    public function getByCompany(string $companyId)
    {
        return CompanyDeviceResource::collection($this->model::where('company_id', $companyId)->get());
    }
}
