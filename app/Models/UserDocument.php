<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserDocument extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getAadhaarCardUrlAttribute()
    {
        if (!empty($this->attributes['aadhaar_card'])) {
            $userId = $this->attributes['user_id'];
            return URL::to(Storage::url('users/' . $userId . '/documents' . '/' . $this->attributes['aadhaar_card']));
        }
        return null;
    }

    public function getPanCardUrlAttribute()
    {
        if (!empty($this->attributes['pan_card'])) {
            $userId = $this->attributes['user_id'];
            return URL::to(Storage::url('users/' . $userId . '/documents' . '/'  . $this->attributes['pan_card']));
        }
        return null;
    }

}
