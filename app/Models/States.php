<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;

    protected $guard = ['id'];
    protected $table="state_list";
    public function country()
    {
        return $this->belongsTo(Country::class); // If you have a separate Country model
    }
    public function cities()
    {
        return $this->hasMany(Cities::class);
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'cities_id', 'city_id');
    }
}
