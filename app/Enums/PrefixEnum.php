<?php

namespace App\Enums;

enum PrefixEnum
{
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

    /**
     * Type electronic document: Contingency Invoice
     */
    public const CONTINGENCY_INVOICE = 'CONTINGENCY_INVOICE';

    const RESOLUTION_EXPIRATION_NOTIFICATION = 'df9047cc-644e-403b-8299-8e73395c13e7';
    const RANK_DEPLETION_NOTIFICATION = '9d7ddb1a-59e5-4a59-ae2e-6270a04335fa';

    const FINAL_AUTHORIZATION_RANGE = 995000000;
    const INITIAL_AUTHORIZATION_RANGE = 1;

    const TYPE = [
        SELF::CREDIT_NOTE,
        SELF::DEBIT_NOTE,
        SELF::INVOICE,
        SELF::SUPPORTING_DOCUMENT,
        SELF::ADJUSTMENT_NOTE,
        SELF::PURCHASE_SUPPLIER,
        SELF::UNASSIGNED
    ];

    const TYPE_RESOLUTION_TRANSLATE = [
        SELF::CREDIT_NOTE => 'Nota crédito',
        SELF::DEBIT_NOTE => 'Nota débito',
        SELF::INVOICE => 'Factura electrónica',
        SELF::SUPPORTING_DOCUMENT => 'Documento soporte',
        SELF::ADJUSTMENT_NOTE => 'Nota de ajuste',
        SELF::PURCHASE_SUPPLIER => 'Orden de compra',
        SELF::UNASSIGNED => 'No asignado',
        SELF::CONTINGENCY_INVOICE => 'Factura electrónica de contingencia',
    ];
}
