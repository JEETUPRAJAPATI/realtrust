<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Feature extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug','image'];


    public function properties()
    {
        return $this->belongsToMany(Property::class)->withTimestamps();
    }
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
    public function getImageUrlAttribute()
    {
        if (!empty($this->attributes['image'])) {

            // Define the path structure
            $path = "feature/" . $this->attributes['image'];
            // dd($path);
            // Check if the file exists in the public storage disk
            if (Storage::disk('public')->exists($path)) {
                // Return the full URL to the image
                return URL::to(Storage::url($path));
            }
        }

        return null;
    }
}
