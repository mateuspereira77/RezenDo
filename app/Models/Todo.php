<?php

namespace App\Models;

use App\Priority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Todo extends Model
{
    use HasFactory;
    protected $fillable = [
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
        return !$this->completed;
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
        return $this->update(['completed' => !$this->completed]);
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
}
