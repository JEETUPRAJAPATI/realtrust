<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;
    protected $table = 'user_interests';

    // Fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'property_id',
        'final_rent',
        'deposit',
        'maintenance_per_month',
        'owner_id',
        'status',
    ];
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'unique_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
