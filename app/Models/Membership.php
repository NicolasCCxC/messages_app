<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory, UuidsTrait;

    const PAYMENT_STATUS_APPROVED = 'APPROVED';
    const PAYMENT_STATUS_DECLINED = 'DECLINED';
    const PAYMENT_STATUS_PENDING = 'PENDING';
    const PAYMENT_STATUS_ERROR = 'ERROR';
    const PAYMENT_METHOD_FREE = 'FREE';
    const PAYMENT_METHOD_PAYU = 'PAYU';
    const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_FREE,
        self::PAYMENT_METHOD_PAYU,
    ];

    const PRICE_USER_MEMBERSHIP = 25000;
    const DISCOUNT_MODULE_MEMBERSHIP = 0.05;
    const FREE_USERS = 3;
    const EXPIRATION_DATE_SEMESTER = 6;
    const EXPIRATION_DATE_MEMBERSHIP = 12;

    // Identifier for the notification type
    public const NOTIFICATION_TYPE = 'PAYMENT_PLANS';

    // Identifier for the notification type related to membership purchases
    public const NOTIFICATION_TYPE_MEMBERSHIP_PURCHASE = '2f3e3247-349e-4f76-a9e4-5c6bb42b302a';

    // Identifier for the module associated with payment plans
    public const MODULE_PAYMENT_PLANS = '01f960d6-af67-4c42-afdb-5f8f91a1bb9a';

    // Identifier for the state of a notification when it has been sent
    public const STATE_NOTIFICATION_SEND = 'a157b0b8-b0bf-36b5-b1bb-10f82deeaec4';

    protected $fillable = [
        'company_id',
        'purchase_date',
        'price',
        'is_active',
        'initial_date',
        'expiration_date',
        'transaction_id',
        'is_first_payment',
        'is_frequent_payment',
        'payment_status',
        'payment_method',
        'invoice_credit_note_id',
        'invoice_credit_note_pdf'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_first_payment' => 'boolean',
        'is_frequent_payment' => 'boolean',
        'price' => 'float',
        'purchase_date' => 'timestamp',
        'initial_date' => 'string',
        'expiration_date' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function modules()
    {
        return $this->hasMany(MembershipHasModules::class);
    }

    public function payTransaction()
    {
        return $this->hasOne(PayTransaction::class);
    }

    public function cancelModulesDetails()
    {
        return $this->belongsTo(CancelModulesDetail::class);
    }
}
