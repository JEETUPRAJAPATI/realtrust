<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at'
    ];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
    ];

    // Define the relationship if needed
    public function tokenable()
    {
        return $this->morphTo();
    }
}
