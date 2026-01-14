<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Politic extends Model
{
    use HasFactory, UuidsTrait;

    const RETURN_POLICY = 'RETURN_POLICY';
    const RIGHT_OF_WITHDRAWAL = 'RIGHT_OF_WITHDRAWAL';
    const WARRANTY_POLICY = 'WARRANTY_POLICY';
    const SHIPPING_POLICY = 'SHIPPING_POLICY';
    const REFUND_POLICIES = 'REFUND_POLICIES';
    const TERMS_AND_CONDITIONS = 'TERMS_AND_CONDITIONS';
    const DATA_PRIVACY_POLICY = 'DATA_PRIVACY_POLICY';

    const LIST_POLITICS = [
        SELF::RETURN_POLICY,
        SELF::RIGHT_OF_WITHDRAWAL,
        SELF::WARRANTY_POLICY,
        SELF::SHIPPING_POLICY,
        SELF::REFUND_POLICIES,
        SELF::TERMS_AND_CONDITIONS,
        SELF::DATA_PRIVACY_POLICY
    ];

    protected $fillable = [
        'type',
        'company_id',
        'bucket_details_id'
    ];

    public function company() : BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

}
