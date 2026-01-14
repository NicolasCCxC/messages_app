<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipPurchaseProcessDetail extends Model
{
    use HasFactory, UuidsTrait;

    protected $table = 'membership_purchase_process_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'purchase_process_id',
        'name',
        'price',
        'module_id',
        'sub_module_id',
    ];

    /**
     * Define the relationship to MembershipPurchaseProcess.
     *
     * @return BelongsTo
     */
    public function purchaseProcess(): BelongsTo
    {
        return $this->belongsTo(MembershipPurchaseProcess::class, 'purchase_process_id', 'id');
    }
}
