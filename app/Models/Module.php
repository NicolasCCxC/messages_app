<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory, UuidsTrait;

    const SECURITY = 'SECURITY';
    const UTILS = 'UTILS';
    const INVENTORY = 'INVENTORY';
    const BUCKET = 'BUCKET';
    const QUALIFICATION = 'QUALIFICATION';
    const BINNACLE = 'BINNACLE';
    const NOTIFICATION = 'NOTIFICATION';
    const INVOICE = 'INVOICE';
    const WEBSITE = 'WEBSITE';
    const ACCOUNTING = 'ACCOUNTING';
    const SHOPPING = 'SHOPPING';
    const ELECTRONIC_INVOICE = 'ELECTRONIC_INVOICE';
    const DOMAIN = 'DOMAIN';
    const PAYS = 'PAYS';
    const PAYROLL = 'PAYROLL';
    const ELECTRONIC_PAYROLL = 'ELECTRONIC_PAYROLL';
    const WEBSITE_NODE = 'WEBSITE_NODE';

    const MODULES = [
        self::SECURITY,
        self::UTILS,
        self::INVENTORY,
        self::BUCKET,
        self::QUALIFICATION,
        self::BINNACLE,
        self::NOTIFICATION,
        self::INVOICE,
        self::WEBSITE,
        self::ACCOUNTING,
        self::SHOPPING,
        self::ELECTRONIC_INVOICE,
        self::DOMAIN,
        self::PAYS
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'state',
        'token'
    ];
}
