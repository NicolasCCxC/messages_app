<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyForeignExchange extends Model
{
    use HasFactory, UuidsTrait;

    protected $table = 'companies_foreign_exchange';
    protected $fillable = [
        'company_id',
        'foreign_exchange_id',
        'is_active'
    ];

    protected $casts = [
        'company_id' => 'string',
        'foreign_exchange_id' => 'string',
        'is_active' => 'boolean',
    ];

    public const COP = 'COP';
    public const AFN = 'AFN';

    public const IDS = [
        self::COP => '0e2346cd-2d32-3383-a762-203a9c013b02',
        self::AFN => '0e4346cd-2d32-3383-a762-203a9c013b02'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
