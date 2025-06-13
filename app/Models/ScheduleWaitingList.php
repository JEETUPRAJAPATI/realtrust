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
}
