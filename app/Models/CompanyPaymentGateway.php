<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPaymentGateway extends Model
{
    use SoftDeletes, UuidsTrait, HasFactory;

    protected $fillable = [
        'payment_gateway_id',
        'credentials',
        'date',
        'company_information_id'
    ];

    protected $casts = [
        'credentials' => 'array'
    ];

    public function companyInformation(): BelongsTo
    {
        return $this->belongsTo(CompanyInformation::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
