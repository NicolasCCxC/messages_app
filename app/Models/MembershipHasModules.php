<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipHasModules extends Model
{
    use HasFactory, UuidsTrait;

    const EXPIRATION_DATE_FREE_MODULES = 12;
    
    const PLANNING_ORGANIZATION = ['id' => 5, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES]; 
    const FREE_MODULES = [
        ['id' => 6, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES],  // Control Interno
        ['id' => 10, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES], // Calendario
        ['id' => 11, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES], // Perfil de la empresa
        ['id' => 12, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES], // Centro de notificaciones
        ['id' => 13, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES], // Reportes analíticos
        ['id' => 1, 'expiration_date' => self::EXPIRATION_DATE_FREE_MODULES],  // Digitalización tienda física
    ];
    const MODULE_WAREHOUSES = 4;
    const PLANNING_ORGANIZATION_ID = 5;
    const MODULE_DIGITAL_SALES = 16;
    const MODULE_INVOICE_ID = 3;
    const MODULE_WEB_SITE = 2;
    const MODULE_PHYSICAL_STORE = 1;
    const PRICE_BASE_ADITIONAL_PAGE = 181150;
    const PRICE_DISCOUNT_ADITIONAL_PAGE = 177600;
    const SUBMODULES_INVOICE_IDS = [1, 2, 3, 4, 11];
    const PURCHASABLE_MODULES = [2, 3, 16];
    const SUBMODULES_INVOICE_UNLIMITED = 11;
    const SUBMODULES_INVOICE_WITH_INVENTORY_ADJUSTMENT = [0, 1, 2, 3, 4, 11];
    const NAME_INVOICE_PLAN = "Documentos electrónicos - Paquete ";
    const DATE_CHANGE = "2024-02-01";

    const NON_DEACTIVABLE_MODULES = [1, 2, 5, 6, 10, 11, 13];


    protected $fillable = [
        'membership_id',
        'membership_modules_id',
        'is_active',
        'percentage_discount',
        'is_frequent_payment',
        'expiration_date',
        'price',
        'price_old',
        'months',
        'name',
    ];

    protected $casts = [
        'percentage_discount' => 'integer',
        'is_active' => 'boolean',
        'is_frequent_payment' => 'boolean',
        'expiration_date' => 'string',
        'price' => 'float',
        'price_old' => 'float',
    ];

    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function membershipSubmodules()
    {
        return $this->hasMany(MembershipSubModule::class);
    }

    public function cancelModulesDetails()
    {
        return $this->belongsTo(CancelModulesDetail::class);
    }
}
