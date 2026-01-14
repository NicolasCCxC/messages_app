<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, UuidsTrait, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'bucket_id',
        'company_id',
        'preview_url',
        'supporting_document_preview_url'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

}
