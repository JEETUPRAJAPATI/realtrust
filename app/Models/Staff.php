<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;
    protected $table = 'staff';
    protected $guard = 'staff';
    protected $fillable = [
        'name',
        'image',
        'email',
        'password',
        'mobile_no',
    ];
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
