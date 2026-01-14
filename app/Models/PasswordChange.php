<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'change_date',
        'change_location',
        'change_device',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
