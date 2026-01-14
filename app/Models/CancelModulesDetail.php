<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelModulesDetail extends Model
{
    use HasFactory, UuidsTrait;

    protected $fillable = [
        'membership_has_modules_id',
        'reason',
        'company_id',
        'membership_id'
    ];

    public function membershipHasModules()
    {
        return $this->hasMany(MembershipHasModules::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
