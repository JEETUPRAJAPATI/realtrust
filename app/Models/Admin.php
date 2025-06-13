<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admins';
    protected $guard = 'admin';
    protected $fillable = [
        'name',
        'image',
        'email',
        'password',
    ];
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
