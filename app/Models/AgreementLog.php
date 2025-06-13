<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AgreementLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',  // ✅ Add this field
        'property_id',
        'owner_id',
        'user_id',
        'agreement',
        'remark',
        'description',
        'owner_approve',
        'user_approve',
        'highlight_owner',
        'owner_approve_btn',
        'user_approve_btn',
        'notary',
        'highlight_user',
        'signature_owner',
        'signature_user',
        'created_at',
        'updated_at'
    ];
    protected $appends = ['agreement_url'];
    public function getAgreementUrlAttribute()
    {
        if (!empty($this->attributes['agreement'])) {
            $property_id = $this->property_id;
            $path = "property/{$property_id}/agreement/" . $this->attributes['agreement'];
            if (Storage::disk('public')->exists($path)) {
                return URL::to(Storage::url($path));
            }
        }
        return null;
    }
    public function properties()
    {
        return $this->hasOne(Property::class, 'unique_id', 'property_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
    // public function agreement()
    // {
    //     return $this->belongsTo(related: AgreementDetail::class, 'id');
    // }
}
