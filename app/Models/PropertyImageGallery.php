<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PropertyImageGallery extends Model
{
    protected $fillable = ['property_id', 'name', 'size'];
    protected $appends = ['image_url'];
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function getImageUrlAttribute()
    {
        if (!empty($this->attributes['name'])) {
            // Dynamically build the path based on owner name and dynamic ID
            $ownerName =  $this->property->owner_id; // Assuming the gallery is related to a property with an owner
            $dynamicId = $this->property->unique_id; // Assuming the unique ID is stored in the property model

            $path = "property/{$ownerName}/{$dynamicId}/gallery/" . $this->attributes['name'];
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                return URL::to(Storage::url($path));
            }
        }

        return null; // Return null if no file is found
    }
}
