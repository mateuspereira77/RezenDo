<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Notifications\TodoAssignedNotification;
use App\Notifications\TodoCompletedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function index(): View
    {
        return view('todos.index');
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
            $assignedUser = \App\Models\User::find($newAssignedToId);
            if ($assignedUser) {
                \Log::info('Enviando notificação para usuário:', ['user_id' => $assignedUser->id, 'user_name' => $assignedUser->name]);
                $assignedUser->notify(new TodoAssignedNotification($todo));
            }
        }

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
            ->orderBy('last_activity_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($todos);
    }

    public function calendar(): View
    {
        return view('todos.calendar');
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
}
