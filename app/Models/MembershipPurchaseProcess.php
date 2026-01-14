<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipPurchaseProcess extends Model
{
    use HasFactory, UuidsTrait;

    protected $table = 'membership_purchase_process';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'company_id',
        'price',
        'is_payment',
        'reference_id',
    ];

    /**
     * Define the relationship to MembershipPurchaseProcessDetail.
     *
     * @return HasMany
     */
    public function purchaseProcessDetails(): HasMany
    {
        return $this->hasMany(MembershipPurchaseProcessDetail::class, 'purchase_process_id', 'id');
    }

    /**
     * Define the relationship to Company.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
