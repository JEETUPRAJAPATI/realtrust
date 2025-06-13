<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];
    public function getIdAttribute($value)
{
    return $value;
}

public function setIdAttribute($value)
{
    $this->attributes['id'] = $value;
}

}
