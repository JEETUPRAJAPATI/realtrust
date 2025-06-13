<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Owner extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'owners';
    protected $fillable = [
        'name',
        'mobile_no',
        'company_name',
        'electricity_bill',
        'pan_card',
        'agreement',
        'employee_id',
        'image',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }
    public function getImageUrlAttribute()
    {
        if ($this->attributes['image']) {
            $userId = $this->attributes['id'];
            return URL::to(Storage::url('owners/' .'/' . $this->attributes['image']));
        }
        return null;
    }


    public function getElectricityBillUrlAttribute()
    {
        if (!empty($this->attributes['electricity_bill'])) {
            $userId = $this->attributes['id'];
            return URL::to(Storage::url('owners/electricity_bill' . $userId . '/documents' . '/' . $this->attributes['electricity_bill']));
        }
        return null;
    }

    public function getPanCardUrlAttribute()
    {
        if (!empty($this->attributes['pan_card'])) {
            $userId = $this->attributes['id'];
            return URL::to(Storage::url('owners/pan_card' . $userId . '/documents' . '/' . $this->attributes['pan_card']));
        }
        return null;
    }

    public function getAgreementUrlAttribute()
    {
        if (!empty($this->attributes['agreement'])) {
            $userId = $this->attributes['id'];
            return URL::to(Storage::url('owners/' . $userId . '/documents' . '/' . $this->attributes['agreement']));
        }
        return null;
    }
}
