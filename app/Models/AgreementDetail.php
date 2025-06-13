<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AgreementDetail extends Model
{
    use HasFactory;

    protected $table = "agreements";


    protected $fillable = [
        'property_id',
        'rent',
        'deposit',
        'monthly_maintenance',
        'contract_duration',
        'contract_renewal_increment',
        'painting_deep_cleaning_charges',
        'notice_period',
        'agreement',
        'owner_id',
        'user_id'
    ];

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

    public function agreementLogs()
    {
        return $this->hasMany(AgreementLog::class, 'property_id', 'property_id')->orderBy('created_at', 'desc');
    }
    public function latestAgreementLogOwner()
    {
        return $this->hasOne(AgreementLog::class, 'property_id', 'property_id')
            ->whereNotNull('owner_id') // Ensure owner_id exists
            ->latest('created_at'); // Fetch latest record for that owner
    }

    public function latestAgreementLogUser()
    {
        return $this->hasOne(AgreementLog::class, 'property_id', 'property_id')
            ->whereNotNull('user_id') // Ensure owner_id exists
            ->latest('created_at'); // Fetch latest record for that owner
    }
}
