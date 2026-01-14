<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrivacyPurpose extends Model
{
    use HasFactory, UuidsTrait;

    protected $table = 'privacy_purposes';
    protected $fillable = [
        'description',
        'is_default'
    ];

    protected $casts = [
        'id' => 'string',
        'description' => 'string',
        'is_default' => 'boolean'
    ];

    public function companies() : BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_privacy_purposes', 'privacy_purpose_id', 'company_id');
    }
}
