<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Society extends Model
{
    use HasFactory;
    public function locality()
    {
        return $this->BelongsTo(Locality::class,'locality_id','id');
    }
     public function properties()
    {
        return $this->hasMany(Property::class, 'society_name', 'id');
    }
}
