<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SharedTodoEditedNotification extends Notification
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

            $editedBy = auth()->check() ? auth()->user()->name : 'Usuário';

            return [
                'todo_id' => $this->todo->id,
                'todo_text' => $this->todo->text,
                'edited_by' => $editedBy,
                'message' => "{$editedBy} editou a tarefa '{$this->todo->text}' que você compartilhou.",
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao criar array de notificação de edição de tarefa compartilhada', [
                'error' => $e->getMessage(),
                'todo_id' => $this->todo->id,
            ]);

            return [
                'todo_id' => $this->todo->id,
                'todo_text' => 'Tarefa',
                'edited_by' => 'Usuário',
                'message' => 'Alguém editou uma tarefa que você compartilhou.',
            ];
        }
    }
}
