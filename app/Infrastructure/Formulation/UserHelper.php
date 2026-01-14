<?php

namespace App\Infrastructure\Formulation;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserHelper
{

    /**
     * @param Model $user
     * @param array $roles
     */
    public static function assignRolesAndPermissions(Model $user, array $roles)
    {
        $user->role()->detach();
        foreach($roles as $rol){
            $newRole= Role::firstOrCreate([
                'company_id' => $user->company_id,
                'name' => $rol['name'],
                'description' => "{$user->company->name}-{$rol['name']}-{$user->name}"
            ]);
            $newRole->permissions()->detach();

            $newRole->users()->attach(['id' => $user->id]);

            if($newRole->name === Role::ADMINISTRATOR_ROLE || $newRole->name === Role::ANALYZE_ROLE){
                $newRole->permissions()->attach(
                    Permission::all()->values()
                );
            }
            else if($newRole->name !== Role::ANALYZE_ROLE){
                foreach ($rol['permissions'] as $permission) {
                    $newRole->permissions()->attach(
                        Permission::firstOrCreate([
                            'name' => $permission['name'],
                            'description' => $permission['description']
                        ])
                    );
                }
            }
        }
    }

    public static function assignSuperAdminRole($idUser)
    {
        $user = User::findOrFail($idUser);

        $newRole= Role::firstOrCreate([
            'company_id' => $user->company_id,
            'name' => 'Super Administrador',
            'description' => "{$user->company->name}-Super Administrador-{$user->name}"
        ]);

        $newRole->users()->attach(['id' => $user->id]);

        $newRole->permissions()->attach(
            Permission::all()->values()
        );

    }

    /**
     * Define the main user of the company
     *
     * @param array $request
     * @param string $company_id
     * @return array
     */
    public static function createCompanyAdminUser(array $request, string $company_id): array
    {
        return [
            'name' => $request['company_representative_name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'company_id' => $company_id,
            'roles' => [
                [
                    'name' => "Administrador",
                    'description' => "{$request['name']}-'Administrador'-{$request['company_representative_name']}",
                    'permissions' => Permission::all()->skip(0)->take(25)->values()
                ]
            ]
        ];
    }
}
