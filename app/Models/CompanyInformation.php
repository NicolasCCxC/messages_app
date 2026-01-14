<?php

namespace App\Models;

use App\Traits\UuidsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyInformation extends Model
{
    use UuidsTrait, HasFactory;

    protected $fillable = ['company_id', 'payment_information'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function companyPaymentsGateway(): HasMany
    {
        return $this->hasMany(CompanyPaymentGateway::class);
    }
}
