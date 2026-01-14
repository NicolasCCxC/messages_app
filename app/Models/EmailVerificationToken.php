<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerificationToken extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = null;

    protected $table = 'email_verification_tokens';

    public $timestamps = false;

    protected $fillable = ['email', 'token', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function isExpired(int $minutes = 15): bool
    {
        return $this->created_at->addMinutes($minutes)->isPast();
    }
}
