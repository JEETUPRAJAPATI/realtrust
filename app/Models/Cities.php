<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;

    protected $table = 'city_list';
    protected $primaryKey = 'city_id'; // ✅ Important: Specify custom PK

    protected $guarded = ['city_id']; // 🔁 Note: `guard` → `guarded`

    public function state()
    {
        return $this->belongsTo(States::class, 'state_id', 'state_id');
    }
}
