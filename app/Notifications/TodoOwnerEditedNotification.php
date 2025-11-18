<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TodoOwnerEditedNotification extends Notification
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
        try {
            // Garantir que os relacionamentos estão carregados
            if (! $this->todo->relationLoaded('user')) {
                $this->todo->load('user:id,name');
            }

            $ownerName = $this->todo->user->name ?? 'Usuário';

            return [
                'todo_id' => $this->todo->id,
                'todo_text' => $this->todo->text,
                'edited_by' => $ownerName,
                'message' => "{$ownerName} editou a tarefa '{$this->todo->text}' que foi compartilhada com você.",
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao criar array de notificação de edição pelo dono', [
                'error' => $e->getMessage(),
                'todo_id' => $this->todo->id,
            ]);

            return [
                'todo_id' => $this->todo->id,
                'todo_text' => 'Tarefa',
                'edited_by' => 'Usuário',
                'message' => 'O dono editou uma tarefa que foi compartilhada com você.',
            ];
        }
    }
}
