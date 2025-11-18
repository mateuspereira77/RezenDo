<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentMentionedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Comment $comment
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
            if (! $this->comment->relationLoaded('todo')) {
                $this->comment->load('todo:id,text');
            }
            if (! $this->comment->relationLoaded('user')) {
                $this->comment->load('user:id,name');
            }

            $mentionedBy = auth()->check() ? auth()->user()->name : $this->comment->user->name;

            return [
                'comment_id' => $this->comment->id,
                'todo_id' => $this->comment->todo_id,
                'todo_text' => $this->comment->todo->text ?? 'Tarefa',
                'mentioned_by' => $mentionedBy,
                'message' => "{$this->comment->user->name} mencionou você em um comentário na tarefa '{$this->comment->todo->text}'.",
            ];
        } catch (\Exception $e) {
            \Log::error('Erro ao criar array de notificação', [
                'error' => $e->getMessage(),
                'comment_id' => $this->comment->id,
            ]);

            return [
                'comment_id' => $this->comment->id,
                'todo_id' => $this->comment->todo_id,
                'todo_text' => 'Tarefa',
                'mentioned_by' => 'Usuário',
                'message' => 'Você foi mencionado em um comentário.',
            ];
        }
    }
}
