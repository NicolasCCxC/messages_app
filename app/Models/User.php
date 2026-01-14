<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, UuidsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'name',
        'password',
        'type',
        'document_number',
        'document_type',
        'company_id',
        'user_privacy_acceptation_date',
        'user_terms_conditions_acceptation_date',
        'last_login',
        'accept_data_policy',
        'accept_terms_conditions',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_privacy_acceptation_date' => 'datetime',
        'last_login' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function role(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'users_id', 'roles_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function scopeUsersByCompany($query)
    {
        if(auth()->user() && auth()->user()->role()->count() > 0 && !auth()->user()->role[0]->name === Role::Main) {
            return $query->whereDoesntHave('role', function ($query) {
                $query->where('name', 'Super Administrador');
            });
        }
    }

    public function scopeSuperUserByCompany($query)
    {
        if(auth()->user() && auth()->user()->role()->count() > 0 && !auth()->user()->role[0]->name === Role::Main) {
            return $query->whereHas('role', function ($query) {
                $query->where('name', 'Super Administrador');
            });
        }
    }
}
