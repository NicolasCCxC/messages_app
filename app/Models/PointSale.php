<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointSale extends Model
{
    use HasFactory, UuidsTrait;

    protected $fillable = [
        'physical_store_id',
        'name',
        'contact_link'
    ];

    public function store()
    {
        return $this->belongsTo(PhysicalStore::class);
    }
}
