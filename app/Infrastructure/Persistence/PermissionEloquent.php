<?php

namespace App\Infrastructure\Persistence;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PermissionEloquent
{



    /**
     * @var Permission
     */
    private $permissionModel;

    /**
     * Class constructor
     *
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permissionModel = $permission;
    }

    /**
     * get all permissions
     *
     * @return Permission[]
     */
    public function getAllPermission ()
    {
        return $this->permissionModel::all()->sortBy('index');
    }

    /**
     * store ner permission
     *
     * @param array $data
     * @return Permission[]
     */
    public function storePermission (array $data)
    {
        $this->permissionModel::create($data);

        return $this->getAllPermission();
    }

    /**
     * get specific permission
     *
     * @param string $name
     * @return Permission
     */
     public function getPermissionByName(string $name)
     {
         return $this->permissionModel::where('name', $name)->first();
     }

    /** Format permission on array
     * @return Collection
     */
    public function formatPermisisons()
    {
        $permissions = $this->getAllPermission()->flatten();
        $response = collect($this->permissionModel::SKELETON_PERMISSIONS)->sortBy('id')->values();

        return $response->map(function ($fatter, $index) use ($permissions) {
            return $this->downLevel($fatter, $permissions);
        });

    }

    public function downLevel(array $data, Collection $collect)
    {
        if(count($data['children']) > 0){
            if($data['merge']){
                $mergeChildren = collect($data['children'])->map(function ($value) use ($collect) {
                    return $this->downLevel($value, $collect);
                });
                $mergeFather = $collect->filter(function ($value) use ($data){
                    return $value->description === $data['name'];
                })->flatten();

                $data['children'] = array_merge($mergeChildren->toArray(),$mergeFather->toArray());
            }else{
                $data['children'] = collect($data['children'])->map(function ($value) use ($collect) {
                    return $this->downLevel($value, $collect);
                });
            }
        }
        else{
            $data['children'] = $collect->filter(function ($value) use ($data){
                if($value->description === $data['name']){
                    $value->parentNode = $this->permissionModel::subModulesToModule[$data['name']];
                }
                return $value->description === $data['name'];
            })->flatten();
        }
        return $data;
    }
}
