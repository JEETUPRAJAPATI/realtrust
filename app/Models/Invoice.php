<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'user_add', 'property_id', 'seller_add','order_id','invoice_date','amount', 'gst_percent', 'total_amount','payment_mode','payment_type','status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
