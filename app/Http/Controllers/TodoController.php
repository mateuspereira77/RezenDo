<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\SharedTodoEditedNotification;
use App\Notifications\TodoAssignedNotification;
use App\Notifications\TodoCompletedNotification;
use App\Notifications\TodoDeletedNotification;
use App\Notifications\TodoOwnerEditedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function index(): View
    {
        return view('todos.index');
    }

    public function help(): View
    {
        return view('help.index');
    }

    public function list(): View
    {
        return view('todos.list');
    }

    public function show(Todo $todo): View
    {
        $this->authorize('view', $todo);

        $todo->load(['assignedTo', 'user', 'sharedWith']);

        return view('todos.show', ['todo' => $todo]);
    }

    public function showHistory(int $id): View
    {
        $userId = auth()->id();

        $todo = Todo::onlyTrashed()
            ->where(function ($query) use ($userId) {
                // Tarefas onde o usuário é o dono
                $query->where('user_id', $userId)
                    // Tarefas onde o usuário é o responsável
                    ->orWhere('assigned_to', $userId)
                    // Tarefas compartilhadas com permissão de escrita
                    ->orWhereHas('sharedWith', function ($q) use ($userId) {
                        $q->where('users.id', $userId)
                            ->where('todo_user.permission', 'write');
                    });
            })
            ->findOrFail($id);

        $todo->load(['assignedTo', 'user', 'sharedWith']);

        return view('todos.show', ['todo' => $todo, 'isDeleted' => true]);
    }

    public function edit(Todo $todo): View
    {
        $this->authorize('view', $todo);

        $todo->load('assignedTo');

        return view('todos.edit', ['todo' => $todo]);
    }

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $assignedTo = $request->input('assigned_to');

        $todo = Todo::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        $todo->load('user'); // Carregar relacionamento user para a notificação

        // Se a tarefa foi criada já atribuída a um usuário, enviar notificação
        if ($assignedTo && (int) $assignedTo !== auth()->id()) {
            $assignedUser = \App\Models\User::find((int) $assignedTo);
            if ($assignedUser) {
                $assignedUser->notify(new TodoAssignedNotification($todo));
            }
        }

        return response()->json($todo, 201);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $oldAssignedTo = $todo->assigned_to;
        $newAssignedTo = $request->input('assigned_to');

        $validated = $request->validated();

        \Log::info('Update Todo - Validated data:', $validated);
        \Log::info('Update Todo - assigned_to value:', ['assigned_to' => $validated['assigned_to'] ?? 'not set']);

        $todo->update($validated);

        $todo->refresh();
        $todo->load(['user', 'assignedTo']); // Carregar relacionamentos para a resposta e notificação

        \Log::info('Update Todo - After update:', [
            'assigned_to' => $todo->assigned_to,
            'assigned_to_user' => $todo->assignedTo?->name,
        ]);

        // Se a tarefa foi atribuída a um novo usuário, enviar notificação
        $oldAssignedToId = $oldAssignedTo ? (int) $oldAssignedTo : null;
        $newAssignedToId = $newAssignedTo ? (int) $newAssignedTo : null;

        if ($newAssignedToId && $newAssignedToId !== $oldAssignedToId && $newAssignedToId !== auth()->id()) {
            $assignedUser = User::find($newAssignedToId);
            if ($assignedUser) {
                \Log::info('Enviando notificação para usuário:', ['user_id' => $assignedUser->id, 'user_name' => $assignedUser->name]);
                $assignedUser->notify(new TodoAssignedNotification($todo));
            }
        }

        // Notificar o dono da tarefa se alguém com quem ela foi compartilhada fez uma edição
        $this->notifyTodoOwnerOnSharedEdit($todo);

        // Notificar usuários com quem a tarefa foi compartilhada quando o dono edita
        $this->notifySharedUsersOnOwnerEdit($todo);

        // Garantir que assigned_to seja sempre retornado, mesmo se null
        $responseData = $todo->toArray();
        $responseData['assigned_to'] = $todo->assigned_to;
        $responseData['assigned_to_user'] = $todo->assignedTo ? [
            'id' => $todo->assignedTo->id,
            'name' => $todo->assignedTo->name,
            'email' => $todo->assignedTo->email,
        ] : null;

        return response()->json($responseData);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $this->authorize('delete', $todo);

        // Só enviar notificações se o usuário for o dono da tarefa
        if ($todo->user_id === auth()->id()) {
            // Carregar relacionamentos necessários antes de deletar
            $todo->load(['sharedWith', 'assignedTo', 'user']);

            // Notificar usuários compartilhados
            foreach ($todo->sharedWith as $user) {
                // Não notificar o próprio criador
                if ($user->id !== auth()->id()) {
                    $user->notify(new TodoDeletedNotification($todo));
                }
            }

            // Notificar usuário atribuído (se diferente do criador)
            if ($todo->assignedTo && $todo->assignedTo->id !== auth()->id()) {
                // Verificar se não está na lista de compartilhados para não duplicar
                $isShared = $todo->sharedWith->contains('id', $todo->assignedTo->id);
                if (! $isShared) {
                    $todo->assignedTo->notify(new TodoDeletedNotification($todo));
                }
            }
        }

        $todo->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso']);
    }

    public function toggle(Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $wasCompleted = $todo->completed;
        $todo->toggleCompletion();
        $todo->refresh();

        // Se a tarefa foi concluída (não estava concluída antes), enviar notificações
        if ($todo->completed && ! $wasCompleted && $todo->user_id === auth()->id()) {
            // Carregar relacionamentos necessários
            $todo->load(['sharedWith', 'assignedTo']);

            // Notificar usuários compartilhados
            foreach ($todo->sharedWith as $user) {
                // Não notificar o próprio criador
                if ($user->id !== auth()->id()) {
                    $user->notify(new TodoCompletedNotification($todo));
                }
            }

            // Notificar usuário atribuído (se diferente do criador)
            if ($todo->assignedTo && $todo->assignedTo->id !== auth()->id()) {
                // Verificar se não está na lista de compartilhados para não duplicar
                $isShared = $todo->sharedWith->contains('id', $todo->assignedTo->id);
                if (! $isShared) {
                    $todo->assignedTo->notify(new TodoCompletedNotification($todo));
                }
            }
        }

        return response()->json($todo);
    }

    public function changePriority(Todo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $priorities = ['simple', 'medium', 'urgent'];
        $currentIndex = array_search($todo->priority, $priorities);
        $nextIndex = ($currentIndex + 1) % count($priorities);
        $todo->update(['priority' => $priorities[$nextIndex]]);

        return response()->json($todo);
    }

    public function all(): JsonResponse
    {
        $todos = Todo::forUserOrShared()
            ->select('todos.*')
            ->selectRaw('COALESCE((SELECT MAX(created_at) FROM comments WHERE comments.todo_id = todos.id), todos.created_at) as last_activity_at')
            ->orderByPriority()
            ->orderBy('last_activity_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($todos);
    }

    public function calendar(): View
    {
        return view('todos.calendar');
    }

    public function history(): View
    {
        return view('todos.history');
    }

    public function productivity(): View
    {
        $userId = auth()->id();

        // Estatísticas gerais (incluindo tarefas deletadas)
        $totalTasks = Todo::withTrashed()->where('user_id', $userId)->count();
        $completedTasks = Todo::withTrashed()->where('user_id', $userId)->where('completed', true)->count();
        $pendingTasks = $totalTasks - $completedTasks;
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // Tarefas por prioridade (incluindo tarefas deletadas)
        $tasksByPriority = [
            'simple' => Todo::withTrashed()->where('user_id', $userId)->where('priority', 'simple')->count(),
            'medium' => Todo::withTrashed()->where('user_id', $userId)->where('priority', 'medium')->count(),
            'urgent' => Todo::withTrashed()->where('user_id', $userId)->where('priority', 'urgent')->count(),
        ];

        // Tarefas concluídas por prioridade (incluindo tarefas deletadas)
        $completedByPriority = [
            'simple' => Todo::withTrashed()->where('user_id', $userId)->where('priority', 'simple')->where('completed', true)->count(),
            'medium' => Todo::withTrashed()->where('user_id', $userId)->where('priority', 'medium')->where('completed', true)->count(),
            'urgent' => Todo::withTrashed()->where('user_id', $userId)->where('priority', 'urgent')->where('completed', true)->count(),
        ];

        // Tarefas atrasadas (com data passada e não concluídas) - apenas não deletadas
        $overdueTasks = Todo::where('user_id', $userId)
            ->where('completed', false)
            ->whereNotNull('date')
            ->where('date', '<', now()->toDateString())
            ->count();

        // Estatísticas dos últimos 30 dias (incluindo tarefas deletadas)
        $last30Days = now()->subDays(30);
        $tasksCreatedLast30Days = Todo::withTrashed()->where('user_id', $userId)
            ->where('created_at', '>=', $last30Days)
            ->count();
        $tasksCompletedLast30Days = Todo::withTrashed()->where('user_id', $userId)
            ->where('completed', true)
            ->where('updated_at', '>=', $last30Days)
            ->count();

        // Timeline dos últimos 7 dias (incluindo tarefas deletadas)
        $timelineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $timelineData[] = [
                'date' => $date,
                'created' => Todo::withTrashed()->where('user_id', $userId)
                    ->whereDate('created_at', $date)
                    ->count(),
                'completed' => Todo::withTrashed()->where('user_id', $userId)
                    ->where('completed', true)
                    ->whereDate('updated_at', $date)
                    ->count(),
            ];
        }

        // Tarefas por dia da semana (últimos 30 dias) (incluindo tarefas deletadas)
        $tasksByDayOfWeek = [];
        $dayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");

        for ($i = 0; $i < 7; $i++) {
            $dayOfWeek = $i; // 0=Domingo, 1=Segunda, ..., 6=Sábado (para strftime)

            if ($connection === 'sqlite') {
                // SQLite: strftime('%w', created_at) retorna 0=Domingo, 1=Segunda, ..., 6=Sábado
                $count = Todo::withTrashed()->where('user_id', $userId)
                    ->where('created_at', '>=', $last30Days)
                    ->whereRaw("strftime('%w', created_at) = ?", [$dayOfWeek])
                    ->count();
            } else {
                // MySQL: DAYOFWEEK retorna 1=Domingo, 2=Segunda, ..., 7=Sábado
                $count = Todo::withTrashed()->where('user_id', $userId)
                    ->where('created_at', '>=', $last30Days)
                    ->whereRaw('DAYOFWEEK(created_at) = ?', [$i + 1])
                    ->count();
            }

            $tasksByDayOfWeek[] = [
                'day' => $dayNames[$i],
                'count' => $count,
            ];
        }

        // Tempo médio de conclusão (em horas) (incluindo tarefas deletadas)
        $completedTodos = Todo::withTrashed()->where('user_id', $userId)
            ->where('completed', true)
            ->whereNotNull('updated_at')
            ->get();

        $totalHours = 0;
        $count = 0;
        foreach ($completedTodos as $todo) {
            $hours = $todo->created_at->diffInHours($todo->updated_at);
            if ($hours > 0) {
                $totalHours += $hours;
                $count++;
            }
        }
        $avgTimeToComplete = $count > 0 ? round($totalHours / $count, 1) : 0;

        return view('todos.productivity', [
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'completionRate' => $completionRate,
            'tasksByPriority' => $tasksByPriority,
            'completedByPriority' => $completedByPriority,
            'overdueTasks' => $overdueTasks,
            'tasksCreatedLast30Days' => $tasksCreatedLast30Days,
            'tasksCompletedLast30Days' => $tasksCompletedLast30Days,
            'timelineData' => $timelineData,
            'tasksByDayOfWeek' => $tasksByDayOfWeek,
            'avgTimeToComplete' => $avgTimeToComplete,
        ]);
    }

    public function getHistory(): JsonResponse
    {
        $userId = auth()->id();

        $todos = Todo::onlyTrashed()
            ->where(function ($query) use ($userId) {
                // Tarefas onde o usuário é o dono
                $query->where('user_id', $userId)
                    // Tarefas onde o usuário é o responsável
                    ->orWhere('assigned_to', $userId)
                    // Tarefas compartilhadas com permissão de escrita
                    ->orWhereHas('sharedWith', function ($q) use ($userId) {
                        $q->where('users.id', $userId)
                            ->where('todo_user.permission', 'write');
                    });
            })
            ->with(['assignedTo', 'user', 'sharedWith'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json($todos);
    }

    public function restore(int $id): JsonResponse
    {
        $todo = Todo::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $todo->restore();

        return response()->json([
            'message' => 'Tarefa restaurada com sucesso.',
            'todo' => $todo,
        ]);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $todo = Todo::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $todo->forceDelete();

        return response()->json(['message' => 'Tarefa excluída permanentemente.']);
    }

    public function getByDateRange(): JsonResponse
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');

        $query = Todo::forUserOrShared()->whereNotNull('date');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $todos = $query->select('todos.*')
            ->selectRaw('COALESCE((SELECT MAX(created_at) FROM comments WHERE comments.todo_id = todos.id), todos.created_at) as last_activity_at')
            ->orderBy('last_activity_at', 'desc')
            ->orderBy('date', 'asc')
            ->orderByPriority()
            ->get();

        return response()->json($todos);
    }

    /**
     * Notificar o dono da tarefa quando alguém com quem ela foi compartilhada fizer uma edição.
     */
    private function notifyTodoOwnerOnSharedEdit(Todo $todo): void
    {
        $editorId = auth()->id();
        $todoOwnerId = $todo->user_id;

        // Não notificar se o editor for o próprio dono da tarefa
        if ($editorId === $todoOwnerId) {
            return;
        }

        // Verificar se o editor tem acesso compartilhado à tarefa
        if (! $todo->isSharedWith($editorId)) {
            return;
        }

        try {
            // Carregar relacionamentos necessários
            $todo->load('user:id,name');
            $todoOwner = User::find($todoOwnerId);

            if ($todoOwner) {
                $todoOwner->notify(new SharedTodoEditedNotification($todo));

                \Log::info('Notificação de edição de tarefa compartilhada enviada ao dono', [
                    'todo_owner_id' => $todoOwnerId,
                    'editor_id' => $editorId,
                    'todo_id' => $todo->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar notificação de edição de tarefa compartilhada', [
                'error' => $e->getMessage(),
                'todo_owner_id' => $todoOwnerId,
                'editor_id' => $editorId,
                'todo_id' => $todo->id,
            ]);
        }
    }

    /**
     * Notificar usuários com quem a tarefa foi compartilhada quando o dono edita.
     */
    private function notifySharedUsersOnOwnerEdit(Todo $todo): void
    {
        $editorId = auth()->id();
        $todoOwnerId = $todo->user_id;

        // Só notificar se o editor for o dono da tarefa
        if ($editorId !== $todoOwnerId) {
            return;
        }

        try {
            // Carregar relacionamentos necessários
            $todo->load('user:id,name');

            // Buscar todos os usuários com quem a tarefa foi compartilhada
            $sharedUsers = $todo->sharedWith()->get();

            foreach ($sharedUsers as $sharedUser) {
                try {
                    $sharedUser->notify(new TodoOwnerEditedNotification($todo));

                    \Log::info('Notificação de edição pelo dono enviada ao usuário compartilhado', [
                        'shared_user_id' => $sharedUser->id,
                        'todo_owner_id' => $todoOwnerId,
                        'todo_id' => $todo->id,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao enviar notificação de edição pelo dono para usuário compartilhado', [
                        'error' => $e->getMessage(),
                        'shared_user_id' => $sharedUser->id,
                        'todo_id' => $todo->id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao notificar usuários compartilhados sobre edição pelo dono', [
                'error' => $e->getMessage(),
                'todo_owner_id' => $todoOwnerId,
                'todo_id' => $todo->id,
            ]);
        }
    }
}
