<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Prefix extends Model
{
    use HasFactory, UuidsTrait;

    /**
     * Type electronic document: Credit Note
     */
    public const CREDIT_NOTE = 'CREDIT_NOTE';
    /**
     * Type electronic document: Debit Note
     */
    public const DEBIT_NOTE = 'DEBIT_NOTE';
    /**
     * Type electronic document: Invoice
     */
    public const INVOICE = 'INVOICE';
    /**
     * Type electronic document: Supporting Document
     */
    public const SUPPORTING_DOCUMENT = 'SUPPORTING_DOCUMENT';
    /**
     * Type electronic document: Supporting Document Note
     */
    public const ADJUSTMENT_NOTE = 'ADJUSTMENT_NOTE';

    /**
     * Type electronic document: Purchase supplier
     */
    public const PURCHASE_SUPPLIER = 'PURCHASE_SUPPLIER';

    /**
     * Type electronic document: unassigned
     */
    public const UNASSIGNED = 'UNASSIGNED';

    const TYPE = [
        SELF::CREDIT_NOTE,
        SELF::DEBIT_NOTE,
        SELF::INVOICE,
        SELF::SUPPORTING_DOCUMENT,
        SELF::ADJUSTMENT_NOTE,
        SELF::PURCHASE_SUPPLIER
    ];

    const RESOLUTION_EXPIRATION_NOTIFICATION = 'df9047cc-644e-403b-8299-8e73395c13e7';
    const RANK_DEPLETION_NOTIFICATION = '9d7ddb1a-59e5-4a59-ae2e-6270a04335fa';

    const FINAL_AUTHORIZATION_RANGE = 995000000;
    const INITIAL_AUTHORIZATION_RANGE = 1;

    protected $fillable = [
        'type',
        'resolution_number',
        'prefix',
        'initial_validity',
        'final_validity',
        'final_authorization_range',
        'initial_authorization_range',
        'physical_store',
        'website',
        'contingency',
        'supporting_document',
        'company_id',
        'resolution_technical_key',
    ];

    protected $casts = [
        'type' => 'string',
        'resolution_number' => 'integer',
        'prefix' => 'string',
        'initial_validity' => 'date',
        'final_validity' => 'date',
        'final_authorization_range' => 'integer',
        'initial_authorization_range' => 'integer',
        'physical_store' => 'boolean',
        'website' => 'boolean',
        'contingency' => 'boolean',
        'supporting_document' => 'boolean',
        'company_id' => 'string',
        'resolution_technical_key' => 'string',
    ];

    protected $attributes = [
        'type' => self::UNASSIGNED, // Default value
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}
