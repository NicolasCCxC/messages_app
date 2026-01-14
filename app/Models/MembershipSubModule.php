<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipSubModule extends Model
{
    use HasFactory, UuidsTrait;

    const SUB_MODULE_WEBSITE_SHOP = 6;
    const SUB_MODULE_WEBSITE_LANDING = 5;
    const SUB_MODULE_EXTRA_PAGE = 7;
    const FREE_INVOICES = 15;
    const SUB_MODULES_WEBSITE_IDS = [5, 6, 7, 10];
    const SUB_MODULES_INVOICES = [1, 2, 3, 4, 11];
    const DEACTIVABLE_SUB_MODULES = [1, 2, 3, 4];


    protected $table = 'membership_submodules';

    protected $fillable = [
        'membership_has_modules_id',
        'sub_module_id',
        'is_active',
        'is_frequent_payment',
        'expiration_date',
        'total_invoices',
        'remaining_invoices',
        'price',
        'price_old',
        'months',
        'name',
        'discount',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_frequent_payment' => 'boolean',
        'price' => 'float',
        'price_old' => 'float',
        'discount' => 'float',
    ];

    public $timestamps = false;

    public function membershipHasModule()
    {
        return $this->belongsTo(MembershipHasModules::class, 'membership_has_modules_id', 'id');
    }

}
