<?php

namespace App\Notifications;

use App\Models\Todo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TodoSharedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Todo $todo,
        public string $permission
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

            $sharedBy = auth()->check() ? auth()->user()->name : $this->todo->user->name;
            $permissionText = $this->permission === 'write' ? 'visualizar e editar' : 'visualizar';

            return [
                'todo_id' => $this->todo->id,
                'todo_text' => $this->todo->text,
                'shared_by' => $sharedBy,
                'permission' => $this->permission,
                'message' => "{$sharedBy} compartilhou a tarefa '{$this->todo->text}' com você. Você pode {$permissionText} esta tarefa.",
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao criar array de notificação de compartilhamento', [
                'error' => $e->getMessage(),
                'todo_id' => $this->todo->id,
            ]);

            return [
                'todo_id' => $this->todo->id,
                'todo_text' => 'Tarefa',
                'shared_by' => 'Usuário',
                'permission' => $this->permission,
                'message' => 'Uma tarefa foi compartilhada com você.',
            ];
        }
    }
}
