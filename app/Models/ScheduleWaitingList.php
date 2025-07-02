<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleWaitingList extends Model
{
    protected $table = 'schedule_waiting_list';

    protected $fillable = [
        'property_id',
        'email',
        'status',
    ];
    public function localities()
    {
        return $this->BelongsTo(Locality::class, 'locality', 'id');
    }
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'unique_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
