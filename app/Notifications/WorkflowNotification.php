<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WorkflowNotification extends Notification
{
    public function __construct(
        private readonly string $category,
        private readonly string $title,
        private readonly string $message,
        private readonly array $details = [],
        private readonly ?string $actionUrl = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'category' => $this->category,
            'title' => $this->title,
            'message' => $this->message,
            'details' => $this->details,
            'action_url' => $this->actionUrl,
        ];
    }
}
