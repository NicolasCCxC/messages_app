<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalStore extends Model
{
    use HasFactory, UuidsTrait;

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'country_id',
        'country_name',
        'department_id',
        'department_name',
        'city_id',
        'city_name',
        'phone'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pointSales()
    {
        return $this->hasMany(PointSale::class);
    }
}
