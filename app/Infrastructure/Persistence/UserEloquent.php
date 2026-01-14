<?php

namespace App\Infrastructure\Persistence;

use App\Http\Resources\UserResource;
use App\Infrastructure\Formulation\BinnacleHelper;
use App\Infrastructure\Formulation\UserHelper;
use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserEloquent
{
    private $model;

    /**
     * @var ClientEloquent
     */
    private $clientEloquent;

    function __construct()
    {
        $this->model = new User();
        $this->clientEloquent = new ClientEloquent();
    }

    /**
     * @param string $idCompany
     *
     * @return AnonymousResourceCollection
     */
    public function getAllCompanyUsers(string $idCompany)
    {
        return UserResource::collection(
            User::query()->where('company_id', $idCompany)
                ->usersByCompany()
                ->orderBy('created_at', 'asc')
                ->get()
        )->push(['users_available' => $this->usersAvailable($idCompany)]);
    }

    /**
     * @param string $idCompany
     *
     * @return User
     */
    public function getSuperUserCompany(string $idCompany)
    {
        return User::query()->where('company_id', $idCompany)
                ->superUserByCompany()
                ->orderBy('created_at', 'asc')
                ->get()
                ->first();
    }

    public static function roleSpecific($permissions)
    {
        return $permissions->map(function ($permission) {
            $parent = collect(Permission::SKELETON_PERMISSIONS)->reduce(function ($carry, $item) use ($permission) {
                $search = collect($item['children'])->first(function ($value) use ($permission) {
                    return $value['name'] === $permission->description;
                });

                if ($search) {
                    return $item['name'];
                }
                return $carry;
            }, '');
            if($parent === ''){
                $parent = $permission->description;
            }
            $permission->parent = $parent;
            return $permission;
        });
    }

    /**
     * Store a new user
     * @param array $user
     * @return AnonymousResourceCollection
     */
    public function storeUser(array $userData, string $ip, array $userConfig = null)
    {
        if (!$userData['accept_policy'] && !isset($userData['company_id'])) {
            return 'You must accept the data policy before continuing';
        }

        //create user
        $flag = true;
        $companyData['name'] =  $userData['name'];
        $userData['password'] = Hash::make($userData['password']);
        if (!isset($userData['company_id'])) {
            $userData['name'] = $userData['email'];
            $userData['accept_data_policy'] = $userData['accept_policy'];
        } else {
            $userData['accept_data_policy'] = false;
        }
        $userData['user_privacy_acceptation_date'] = now();
        $userData['accept_terms_conditions'] = $userData['accept_terms'];
        $userData['user_terms_conditions_acceptation_date'] = now();
        if(isset($userData['nit']) && !isset($userData['company_id'])){
            $companyData['document_number'] = $userData['nit'];
            $companyData['document_type'] =  $userData['document_type'];
            $companyData['phone'] =  isset($userData['phone']) ? $userData['phone'] : null;
            $companyData['company_representative_name'] = isset($userData['company_representative_name']) ? $userData['company_representative_name'] : '';
            $companyData['brand_established_service'] = false;
            $companyData['accept_company_privacy'] = true;
            $company = Company::create($companyData);
            $userData['company_id'] = $company->id;
            $flag = false;
        }
        $user = $this->model->query()->create($userData);

        //if have roles, then it add him
        if (isset($userData['roles'])) {
            $roles = $userData['roles'];
            UserHelper::assignRolesAndPermissions($user, $roles);
        } else {
            $this->assignCompanyAndSuperAdmin($user->id, $company->id);
        }

        if (isset($userData['company_id']) && $flag) {
            BinnacleHelper::internalActivity(
                $ip,
                $userConfig === null
                    ? Auth::user()->id
                    : $this->model::where('company_id', $userConfig['company_id'])
                    ->where('email', $userConfig['email'])
                    ->first()
                    ->company_id,
                $userConfig === null ? (Auth::user()->name ?? Auth::user()->email) : $userConfig['name'],
                $userConfig === null ? Auth::user()->email : $userConfig['email'],
                $userConfig === null ? Auth::user()->company_id : $userConfig['company_id'],
                'Perfil de la empresa',
                'Agregó usuario'
            );
        }
        return !$flag ? new UserResource($user) : $this->getAllCompanyUsers($user->company_id);
    }

    public function assignCompanyAndSuperAdmin(string $idUser, string $idCompany)
    {
        $user = $this->model::findOrFail($idUser);
        $user->company_id = $idCompany;
        $user->save();
        UserHelper::assignSuperAdminRole($user->id);

    }

    /**
     * Get a user by id
     * @param string $id
     * @return array
     */
    function getUserById(string $id)
    {
        return UserResource::make(User::query()->findOrFail($id));
    }

    /**
     * Get a user by companyId
     * @param string $companyId
     * @return array
     */
    function getUserByCompanyId(string $companyId)
    {
        return User::query()->where('company_id',$companyId)->orderBy('created_at', 'asc')->first();
    }

    /**
     * Update a user
     *
     * @param array $userUpdateFields
     * @param string $id
     *
     * @return mixed
     */
    public function updateUser(array $userUpdateFields, string $ip)
    {
        $user = User::query()->findOrFail($userUpdateFields['id']);
        $role = $user->role[0]->name;

        // Validates that only the super administrator can modify his data.
        if($role === Role::Main && Auth::user()->id != $user->id){
            return [
                'statusCode' => Response::HTTP_FORBIDDEN,
                'message' => 'You do not have permission to modify the super administrator'
            ];
        }

        $user->fill($userUpdateFields);

        if (array_key_exists('password', $userUpdateFields)) {
            $user['password'] = Hash::make($user['password']);
        }

        //Prevents the super administrator from being able to modify his role and Delete all permission of user
        if ($role != Role::Main && array_key_exists('roles', $userUpdateFields)) {
            $rolesCollectionId = collect($user->role)->map(function ($role) {
                return $role['id'];
            });
            $user->role()->detach($rolesCollectionId);
            $roles = $userUpdateFields['roles'];
            UserHelper::assignRolesAndPermissions($user, $roles);
        }

        $user->save();

        if ($user->company_id) {
            BinnacleHelper::internalActivity(
                $ip,
                Auth::user()->id,
                Auth::user()->name ?? Auth::user()->email,
                Auth::user()->email,
                Auth::user()->company_id,
                'Perfil de la empresa',
                'Modificó usuario'
            );
        }


        return $user->company_id ? $this->getAllCompanyUsers($user->company_id) : UserResource::make($user);
    }

    /**
     *
     *
     * @param array $data
     * @param string $idCompany
     * @return AnonymousResourceCollection
     */
    public function userSoftDelete(array $data, string $idCompany, string $ip)
    {
        foreach ($data as $item) {
            $user = User::query()->findOrFail($item['id']);
            $user->delete();
        }

        BinnacleHelper::internalActivity(
            $ip,
            Auth::user()->id,
            Auth::user()->name ?? Auth::user()->email,
            Auth::user()->email,
            Auth::user()->company_id,
            'Perfil de la empresa',
            'Eliminó usuario'
        );

        return $this->getAllCompanyUsers($idCompany);
    }

    public function getAllUsers(string $companyId)
    {
        return $this->model::where('company_id', $companyId)
            ->get();
    }

    public function filterbyUserPermission(string $companyId)
    {
        return $this->model::whereHas('role', function ($query) {
            $query->where('name', '!=', Role::ANALYZE_ROLE);
        })->where('company_id', $companyId)->select(['id', 'name', 'email'])->get();
    }

    public function updateFirstLogin(string $id)
    {
        $user = User::findOrFail($id);
        $user->is_first_login = false;
        $user->save();

        return $user;
    }

    public function usersAvailable(string $companyId)
    {
        $company = Company::find($companyId);
        $numberUsers = $this->model::where('company_id', $companyId)->count();
        return ($company->users_available - $numberUsers + 1);
    }

    /**
     * @param $companyId
     * @param UpdateCompanyAccountCreated $request
     * @return CompanyResource
     * @throws GuzzleException
     */
    public function getUsersActiveMembership()
    {
        $users = User::select('id', 'email', 'company_id')
        ->whereHas('role', fn ($q) => $q->where('name', Role::Main))
        ->whereHas('company.memberships', function ($query) {
            $query->whereRaw('expiration_date::date > now()')
                  ->whereNotNull('expiration_date')
                  ->where('is_active', true);
        })
        ->orderBy('created_at')
        ->get();
        return $users->unique('company_id')->values();
    }
}
