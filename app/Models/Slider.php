<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Slider extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        if (!empty($this->attributes['image'])) {
            $path = "slider/" . $this->attributes['image'];
            if (Storage::disk('public')->exists($path)) {
                return URL::to(Storage::url($path));
            }
        }

        return null; // Return null if no file is found
    }
}
