<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityFiscalResponsibility extends Model
{
    use HasFactory, UuidsTrait;

    protected $fillable = [
        'code_fiscal_responsibility',
        'company_id',
        'date',
        'number_resolution',
        'withholdings'
    ];

    protected $casts = [
        'withholdings' => 'array'
    ];

    public $timestamps = false;

    public function company (): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
