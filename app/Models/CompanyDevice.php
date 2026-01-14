<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDevice extends Model
{
    use HasFactory, UuidsTrait;

    protected $table = 'companies_devices';

    protected $fillable = [
        'name',
        'company_id'
    ];

    protected $casts = [
        'name' => 'string',
        'company_id' => 'string'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
