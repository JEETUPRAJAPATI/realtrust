<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentes extends Model
{
    use HasFactory;

    protected $table = 'payments'; // Ensure this is correct
    protected $fillable = ['property_id', 'name', 'email', 'mobile', 'currency', 'amount', 'method', 'order_id', 'signature', 'payment_id','payment_type', 'json_response', 'status'];


    // Define relationship with Property model
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
