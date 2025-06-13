<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConformTiming extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'field_manager_id',
        'timing',
        'conform_timing',
        'gate_pass',
        'flat_number',
        'key_person_number',
        'created_at',
        'updated_at'
    ];
    protected $table = 'conform_timing';
    public function field_manager()
    {
        return $this->belongsTo(FieldManager::class);
    }
    public function properties()
    {
        return $this->hasOne(Property::class, 'unique_id', 'property_id');
    }
    public function scheduleVisit()
    {
        return $this->belongsTo(ScheduleVisit::class, 'property_id', 'property_id'); // Assuming 'property_id' in both tables is the key
    }
}
