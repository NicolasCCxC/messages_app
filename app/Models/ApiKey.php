<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use UuidsTrait;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'auth_key',
        'security_key',
        'name'
    ];

    public function companies ()
    {
        return $this->belongsToMany(Company::class,'companies_api_keys','api_keys_id','companies_id');
    }
}
