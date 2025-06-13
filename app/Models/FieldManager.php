<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FieldManager extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'field_manager';
    protected $guarded = ['id'];

    public function conform_timing()
    {
        return $this->belongsTo(ConformTiming::class, 'id', 'field_manager_id');
    }
}
