<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    use HasFactory;


    protected $table = 'localities';
    public function state()
    {
        return $this->belongsTo(States::class, 'state_id', 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'cities_id', 'city_id');
    }
    public function societies()
    {
        return $this->hasMany(Society::class);
    }
    
    public function properties()
    {
        return $this->hasMany(Property::class, 'locality', 'id');
    }

}
