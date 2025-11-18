<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TodoCompletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Todo $todo
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'todo_id' => $this->todo->id,
            'todo_text' => $this->todo->text,
            'completed_by' => auth()->user()->name,
            'message' => "A tarefa '{$this->todo->text}' foi concluída por {$this->todo->user->name}.",
        ];
    }
}
