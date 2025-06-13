<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleVisit extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'unique_id');
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userLists()
    {
        return $this->hasMany(ScheduleVisitUserList::class, 'visite_id');
    }

    // Relationship to Field Manager
    public function field_manager()
    {
        return $this->belongsTo(FieldManager::class);
    }

    // Optional: Relationship to Owner
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
    public function conform_timing()
    {
        return $this->hasOne(ConformTiming::class, 'property_id', 'property_id'); // Ensure foreign and local keys are correct
    }
}
