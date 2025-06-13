<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Property extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function getFlorImageUrlAttribute()
    {
        if (!empty($this->attributes['floor_plan'])) {
            // Dynamically build the path based on owner name and dynamic ID
            $ownerName = $this->owner_id; // Assuming the property has a related owner
            $dynamicId = $this->attributes['unique_id']; // Assuming the unique_id is stored in the property model

            $path = "property/{$ownerName}/{$dynamicId}/" . $this->attributes['floor_plan'];
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                $url = URL::to(Storage::url($path));
                Log::info("Floor Plan URL: " . $url);
                return $url;
            }
        }

        return null; // Return null if no file is found
    }

    public function getPdfFileUrlAttribute()
    {
        if (!empty($this->attributes['pdf_file'])) {
            $ownerName = $this->owner_id;
            $dynamicId = $this->attributes['unique_id'];
            $path = "property/{$ownerName}/{$dynamicId}/" . $this->attributes['pdf_file'];
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                $url = URL::to(Storage::url($path));
                Log::info("Floor Plan URL: " . $url);
                return $url;
            }
        }

        return null; // Return null if no file is found
    }
    public function getImageUrlAttribute()
    {
        if (!empty($this->attributes['image'])) {
            // Dynamically build the path based on owner name and dynamic ID
            $ownerName = $this->owner_id;  // Assuming the property has a related owner
            $dynamicId = $this->attributes['unique_id']; // Assuming the unique_id is stored in the property model

            $path = "property/{$ownerName}/{$dynamicId}/" . $this->attributes['image'];
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                return URL::to(Storage::url($path));
            }
        }

        return null; // Return null if no file is found
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class)->withTimestamps();
    }
    public function amenities()
    {
        return $this->belongsToMany(Amenities::class)->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
    public function gallery()
    {
        return $this->hasMany(PropertyImageGallery::class, 'property_id', 'id');
    }
    public function video()
    {
        return $this->hasMany(PropertyImageGallery::class, 'property_id', 'id');
    }
    public function schedule_visit()
    {
        return $this->belongsTo(ScheduleVisit::class, 'unique_id', 'property_id');
    }
    // public function schedulePropertyTiming()
    // {
    //     return $this->belongsTo(ScheduleVisit::class, 'unique_id', 'property_id')
    //         //  2024-10-09 14:29:05 > 2024-11-11
    //         ->whereRaw("
    //         STR_TO_DATE(SUBSTRING_INDEX(timing, ' - ', 1), '%m/%d/%Y %h:%i %p') > NOW()
    //     ")
    //     ->where('status', 'sending');
    // }
    public function schedulePropertyTiming()
    {
        return $this->belongsTo(ScheduleVisit::class, 'unique_id', 'property_id')->where('status', 'sending');
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
    // public function cities()
    // {
    //     return $this->belongsTo(Cities::class, 'city', 'city_id');
    // }
    // public function rating()
    // {
    //     return $this->hasMany(Rating::class, 'property_id');
    // }
}
