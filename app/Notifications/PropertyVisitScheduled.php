<?php

namespace App\Notifications;

use App\Models\FieldManager;
use App\Models\Owner;
use App\Models\Property;
use App\Models\ScheduleProperties;
use App\Models\ScheduleVisit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PropertyVisitScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $property;
    protected $user;
    protected $visit;

    public function __construct(Property $property, User $user, ScheduleProperties $visit)
    {
        $this->property = $property;
        $this->user = $user;
        $this->visit = $visit;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'property_id' => $this->property->unique_id,
            'user_name' => $this->user->name,
            'visit_date' => $this->visit->created_at,
            'schedule_visit_id' => $this->visit->id,
            'message' => "A new user has scheduled a visit for the property with ID: {$this->property->unique_id}. Please review the details and prepare for the upcoming appointment.",
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'property_id' => $this->property->unique_id,
            'user_name' => $this->user->name,
            'visit_date' => $this->visit->created_at,
            'schedule_visit_id' => $this->visit->id,
            'message' => 'A new visit has been scheduled.',
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'property_id' => $this->property->unique_id,
            'user_name' => $this->user->name,
            'visit_date' => $this->visit->created_at,
            'message' => 'A new visit has been scheduled.',
        ];
    }
}
