<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard = "user";
    protected $fillable = [
        'name',
        'mobile_no',
        'company_name',
        'image',
        'employee_id',

        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }
    public function userDocuments()
    {
        return $this->hasMany(UserDocument::class, 'user_id');
    }
    public function getImageUrlAttribute()
    {
        if ($this->attributes['image']) {
            $userId = $this->attributes['id'];
            return URL::to(Storage::url('users/' .'/'  . $this->attributes['image']));
        }
        return null;
    }

    
    public function getAgreementUrlAttribute()
    {
        if (!empty($this->attributes['agreement'])) {
            $userId = $this->attributes['id'];
            return URL::to(Storage::url('users/' . $userId . '/documents' . '/' . $this->attributes['agreement']));
        }
        return null;
    }
}
