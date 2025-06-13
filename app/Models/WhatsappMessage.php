<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'unique_id',
        'phone_number',
        'template_name',
        'variables',
        'message_id',
        'status',
        'api_response',
        'sent_at',
    ];

    protected $casts = [
        'variables' => 'array',
        'api_response' => 'array',
    ];
}
