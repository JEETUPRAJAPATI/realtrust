<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'field_manager_id',
        'status',
        'otp_verification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the field manager that owns the message.
     */
    public function fieldManager()
    {
        return $this->belongsTo(FieldManager::class);
    }
}
