<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTransaction extends Model
{
    use HasFactory, UuidsTrait;

    const PSE = 'PSE';
    const PAYMENT_STATUS_PENDING = 'PENDING';
    const PAYMENT_STATUS_APPROVED = 'APPROVED';
    const PAYMENT_STATUS_DECLINED = 'DECLINED';

    protected $fillable = [
        'transaction_id',
        'membership_id',
        'company_id',
        'users_quantity',
        'invoices_quantity',
        'status',
        'json_invoice',
        'pages_quantity',
        'json_pse_url_response'
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
