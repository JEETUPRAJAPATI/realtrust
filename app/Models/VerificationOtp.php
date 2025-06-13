<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationOtp extends Model
{
    use HasFactory;
    protected $table = "verification_opt";
    protected $fillable = [
        'user_id',
        'owner_id',
        'field_manager_id',
        'otp',
        'expires_at',
        'is_used',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
