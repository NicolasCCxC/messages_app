<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, UuidsTrait, SoftDeletes;

    protected $fillable = [
        'reference',
        'date_approved',
        'date_payment',
        'client_id',
        'amount',
        'company_information_id',
        'company_payment_gateway_id',
        'status',
        'purchase_order_id',
        'payment_method_id',
        'payment_number',
        'url_pdf',
        'url_html',
    ];

    public function companyInformation(): BelongsTo
    {
        return $this->belongsTo(CompanyInformation::class);
    }

    public function companyPaymentGateway(): BelongsTo
    {
        return $this->belongsTo(CompanyPaymentGateway::class);
    }
}
