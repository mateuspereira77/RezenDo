<?php

namespace App\Models;

use App\Priority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'text',
        'description',
        'completed',
        'priority',
        'day',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'date' => 'date',
            'priority' => Priority::class,
        ];
    }

    /**
     * Scope para tarefas pendentes.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('completed', false);
    }

    /**
     * Scope para tarefas concluídas.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('completed', true);
    }

    /**
     * Scope para ordenar por prioridade (urgent → medium → simple).
     */
    public function scopeOrderByPriority(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE 
                WHEN priority = 'urgent' THEN 1
                WHEN priority = 'medium' THEN 2
                WHEN priority = 'simple' THEN 3
                ELSE 4
            END
        ");
    }

    /**
     * Scope para filtrar por prioridade.
     */
    public function scopeByPriority(Builder $query, Priority|string $priority): Builder
    {
        $priorityValue = $priority instanceof Priority ? $priority->value : $priority;

        return $query->where('priority', $priorityValue);
    }

    /**
     * Scope para tarefas com data específica.
     */
    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope para tarefas sem data.
     */
    public function scopeWithoutDate(Builder $query): Builder
    {
        return $query->whereNull('date');
    }

    /**
     * Verifica se a tarefa está pendente.
     */
    public function isPending(): bool
    {
        return ! $this->completed;
    }

    /**
     * Verifica se a tarefa está concluída.
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * Verifica se a tarefa é urgente.
     */
    public function isUrgent(): bool
    {
        return $this->priority === Priority::URGENT;
    }

    /**
     * Verifica se a tarefa tem data definida.
     */
    public function hasDate(): bool
    {
        return $this->date !== null;
    }

    /**
     * Alterna o status de conclusão da tarefa.
     */
    public function toggleCompletion(): bool
    {
        return $this->update(['completed' => ! $this->completed]);
    }

    /**
     * Marca a tarefa como concluída.
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['completed' => true]);
    }

    /**
     * Marca a tarefa como pendente.
     */
    public function markAsPending(): bool
    {
        return $this->update(['completed' => false]);
    }

    /**
     * Obtém o rótulo da prioridade.
     */
    public function getPriorityLabelAttribute(): string
    {
        return $this->priority?->label() ?? 'Simples';
    }

    /**
     * Obtém a cor da prioridade.
     */
    public function getPriorityColorAttribute(): string
    {
        return $this->priority?->color() ?? 'green';
    }

    /**
     * Relacionamento com o usuário proprietário da tarefa.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar tarefas do usuário autenticado.
     */
    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    /**
     * Relacionamento many-to-many com usuários que têm acesso compartilhado à tarefa.
     */
    public function sharedWith(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'todo_user')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Scope para incluir tarefas compartilhadas ou atribuídas ao usuário autenticado.
     */
    public function scopeForUserOrShared(Builder $query, ?int $userId = null): Builder
    {
        $userId = $userId ?? auth()->id();

        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('assigned_to', $userId)
                ->orWhereHas('sharedWith', function ($q) use ($userId) {
                    $q->where('users.id', $userId);
                });
        });
    }

    /**
     * Verifica se a tarefa está compartilhada com um usuário específico.
     */
    public function isSharedWith(int $userId): bool
    {
        return $this->sharedWith()->where('users.id', $userId)->exists();
    }

    /**
     * Verifica se o usuário tem permissão de escrita na tarefa compartilhada.
     */
    public function hasWritePermission(int $userId): bool
    {
        if ($this->user_id === $userId) {
            return true;
        }

        return $this->sharedWith()
            ->where('users.id', $userId)
            ->wherePivot('permission', 'write')
            ->exists();
    }

    /**
     * Relacionamento com os comentários da tarefa.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relacionamento com o usuário responsável pela tarefa.
     */
    public function assignedTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope para filtrar tarefas atribuídas a um usuário.
     */
    public function scopeAssignedTo(Builder $query, ?int $userId = null): Builder
    {
        return $query->where('assigned_to', $userId ?? auth()->id());
    }
}
