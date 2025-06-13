<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleProperties extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'unique_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to Field Manager
    public function field_manager()
    {
        return $this->belongsTo(FieldManager::class);
    }
    public function conform_timing()
    {
        return $this->belongsTo(ConformTiming::class, 'property_id', 'property_id');
    }
    public function schedule_visit()
    {
        return $this->belongsTo(Property::class, 'property_id', 'unique_id');
    }
    public function schedule_visit_date()
    {
        return $this->belongsTo(ScheduleVisit::class, 'property_id', 'property_id');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }


    public function society()
    {
        return $this->belongsTo(Society::class, 'society_name', 'id');
    }

    // Relationship with Locality
    public function locality()
    {
        return $this->BelongsTo(Locality::class, 'locality', 'id');
    }

    // Relationship with City
    public function city()
    {
        return $this->belongsTo(Cities::class, 'city', 'city_id');
    }
    public function localities()
    {
        return $this->BelongsTo(Locality::class, 'locality', 'id');
    }

    // Relationship with City
    public function cities()
    {
        return $this->belongsTo(Cities::class, 'city', 'city_id');
    }
}
