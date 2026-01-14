<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 */
class Company extends Model
{

    use HasFactory, UuidsTrait;

    protected $table = 'companies';
    protected $keyType = 'string';

    const COMPANY_CCXC = "83e80ae5-affc-32b4-b11d-b4cab371c48b";
    const NATURAL_PERSON = 'NATURAL_PERSON';
    const NATURAL_PERSON_ID = "c8dfbea8-11ca-35bb-bea2-3dc15b66af64";
    const NATURAL_PERSON_MERCHANT = 'NATURAL_PERSON_MERCHANT';
    const NATURAL_PERSON_MERCHANT_ID = "3bdcf2b7-38d5-4a68-b194-023909cb904a";
    const LEGAL_PERSON_ID = "02287cd4-2eaf-3e16-a341-1f894429aebd";
    const LEGAL_PERSON = 'LEGAL_PERSON';
    const FREE_INVOICES = 15;

    const PERSON_TYPES = [
        self::NATURAL_PERSON_MERCHANT,
        self::NATURAL_PERSON,
        self::LEGAL_PERSON
    ];

    const PERSON_TYPES_ID = [
        self::NATURAL_PERSON_MERCHANT => self::NATURAL_PERSON_MERCHANT_ID,
        self::NATURAL_PERSON => self::NATURAL_PERSON_ID,
        self::LEGAL_PERSON => self::LEGAL_PERSON_ID
    ];

    protected $hidden = ['users_available', 'invoices_available'];

    protected $fillable = [
        'name',
        'person_type',
        'document_type',
        'document_number',
        'company_representative_name',
        'phone',
        'country_id',
        'country_name',
        'foreign_exchange_id',
        'foreign_exchange_code',
        'department_id',
        'department_name',
        'city_id',
        'city_name',
        'postal_code',
        'address',
        'domain',
        'make_web_page_type',
        'brand_established_service',
        'accept_company_privacy',
        'has_a_physical_store',
        'has_e_commerce',
        'company_privacy_acceptation_date',
        'whatsapp',
        'tax_detail',
        'invoices_available',
        'users_available',
        'pages_available',
        'is_billing_us',
    ];

    protected $casts = [
        'name' => 'string',
        'person_type' => 'string',
        'document_type' => 'string',
        'document_number' => 'string',
        'foreign_exchange_id' => 'string',
        'foreign_exchange_code' => 'string',
        'company_representative_name' => 'string',
        'ciiu_id' => 'integer',
        'ciiu_code' => 'string',
        'phone' => 'integer',
        'country_id' => 'integer',
        'country_name' => 'string',
        'department_id' => 'integer',
        'department_name' => 'string',
        'city_id' => 'integer',
        'city_name' => 'string',
        'postal_code' => 'string',
        'address' => 'string',
        'domain' => 'string',
        'make_web_page_type' => 'string',
        'brand_established_service' => 'boolean',
        'accept_company_privacy' => 'boolean',
        'has_a_physical_store' => 'boolean',
        'has_e_commerce' => 'boolean',
        'company_privacy_acceptation_date' => 'date',
        'fiscal_responsibility' => 'string',
        'whatsapp' => 'integer',
        'invoices_available' => 'double',
        'users_available' => 'double',
        'pages_available' => 'double',
        'is_billing_us' => 'boolean',
    ];

    protected $dates = [
        'company_privacy_acceptation_date'
    ];


    public function apiKeys(): BelongsToMany
    {
        return $this->belongsToMany(ApiKey::class, 'companies_api_keys', 'companies_id', 'api_keys_id');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(ApiKey::class, 'companies_clients', 'company_id', 'client_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }


    public function politics() : HasMany
    {
        return $this->hasMany(Politic::class);
    }

    public function ciius()
    {
        return $this->hasMany(Ciiu::class);
    }

    public function prefixes()
    {
        return $this->hasMany(Prefix::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function fiscalResponsibilities(): HasMany
    {
        return $this->hasMany(SecurityFiscalResponsibility::class);
    }

    public function role(): HasMany
    {
        return $this->hasMany(Role::class, 'company_id', 'id');
    }

    public function CompanyForeignExchange ()
    {
        return $this->hasMany(CompanyForeignExchange::class);
    }

    public function cancelModulesDetails()
    {
        return $this->belongsTo(CancelModulesDetail::class);
    }

    public function companyDevices(): HasMany
    {
        return $this->hasMany(CompanyDevice::class);
    }

    public function privacyPurposes() : BelongsToMany
    {
        return $this->belongsToMany(PrivacyPurpose::class, 'company_privacy_purposes', 'company_id', 'privacy_purpose_id');
    }
}
