<?php

namespace App\Notifications;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConsultationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Consultation $consultation,
        public string $title,
        public string $message,
        public string $actionUrl,
        public string $eventType = 'info'
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'consultation_id' => $this->consultation->id,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'event_type' => $this->eventType,
        ];
    }
}