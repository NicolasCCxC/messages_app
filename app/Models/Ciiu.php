<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciiu extends Model
{
    use HasFactory, UuidsTrait;


    protected $table = 'ciius_company';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'is_main',
        'ciiu_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
