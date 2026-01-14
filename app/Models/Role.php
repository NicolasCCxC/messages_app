<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    use HasFactory, UuidsTrait;

    const ADMINISTRATOR_ROLE = "Administrador";
    const ANALYZE_ROLE = "Leer y analizar";
    const Main = 'Super Administrador';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'company_id'
    ];

    public function permissions ()
    {
        return $this->belongsToMany(Permission::class,'roles_permissions','roles_id','permissions_id');
    }

    public function users ()
    {
        return $this->belongsToMany(User::class,'users_roles','roles_id','users_id',);
    }
}
