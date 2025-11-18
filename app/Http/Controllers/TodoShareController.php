<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareTodoRequest;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoSharedNotification;
use Illuminate\Http\JsonResponse;

class TodoShareController extends Controller
{
    /**
     * Compartilhar uma tarefa com um usuário.
     */
    public function store(ShareTodoRequest $request, Todo $todo): JsonResponse
    {
        // Verificar se o usuário é o dono da tarefa
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['message' => 'Apenas o dono da tarefa pode compartilhá-la.'], 403);
        }

        $userId = $request->user_id;

        // Verificar se já está compartilhada
        if ($todo->isSharedWith($userId)) {
            // Atualizar permissão se já estiver compartilhada
            $todo->sharedWith()->updateExistingPivot($userId, [
                'permission' => $request->permission,
            ]);

            // Notificar o usuário sobre a atualização de permissão
            $this->notifySharedUser($todo, $userId, $request->permission);

            return response()->json([
                'message' => 'Permissão atualizada com sucesso.',
                'shared_with' => $todo->sharedWith()->where('users.id', $userId)->first(),
            ]);
        }

        // Compartilhar a tarefa
        $todo->sharedWith()->attach($userId, [
            'permission' => $request->permission,
        ]);

        // Notificar o usuário com quem a tarefa foi compartilhada
        $this->notifySharedUser($todo, $userId, $request->permission);

        return response()->json([
            'message' => 'Tarefa compartilhada com sucesso.',
            'shared_with' => $todo->sharedWith()->where('users.id', $userId)->first(),
        ], 201);
    }

    /**
     * Listar usuários com quem a tarefa foi compartilhada.
     */
    public function index(Todo $todo): JsonResponse
    {
        // Verificar se o usuário é o dono ou tem acesso
        if ($todo->user_id !== auth()->id() && ! $todo->isSharedWith(auth()->id())) {
            return response()->json(['message' => 'Você não tem permissão para ver esta informação.'], 403);
        }

        $sharedWith = $todo->sharedWith()->get();

        return response()->json($sharedWith);
    }

    /**
     * Remover compartilhamento de uma tarefa.
     */
    public function destroy(Todo $todo, int $userId): JsonResponse
    {
        // Verificar se o usuário é o dono da tarefa
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['message' => 'Apenas o dono da tarefa pode remover compartilhamentos.'], 403);
        }

        // Verificar se está compartilhada
        if (! $todo->isSharedWith($userId)) {
            return response()->json(['message' => 'Esta tarefa não está compartilhada com este usuário.'], 404);
        }

        $todo->sharedWith()->detach($userId);

        return response()->json(['message' => 'Compartilhamento removido com sucesso.']);
    }

    /**
     * Atualizar permissão de compartilhamento.
     */
    public function update(ShareTodoRequest $request, Todo $todo, int $userId): JsonResponse
    {
        // Verificar se o usuário é o dono da tarefa
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['message' => 'Apenas o dono da tarefa pode atualizar permissões.'], 403);
        }

        // Verificar se está compartilhada
        if (! $todo->isSharedWith($userId)) {
            return response()->json(['message' => 'Esta tarefa não está compartilhada com este usuário.'], 404);
        }

        $todo->sharedWith()->updateExistingPivot($userId, [
            'permission' => $request->permission,
        ]);

        // Notificar o usuário sobre a atualização de permissão
        $this->notifySharedUser($todo, $userId, $request->permission);

        return response()->json([
            'message' => 'Permissão atualizada com sucesso.',
            'shared_with' => $todo->sharedWith()->where('users.id', $userId)->first(),
        ]);
    }

    /**
     * Notificar o usuário quando uma tarefa é compartilhada com ele.
     */
    private function notifySharedUser(Todo $todo, int $userId, string $permission): void
    {
        try {
            // Carregar relacionamentos necessários
            $todo->load('user:id,name');
            $sharedUser = User::find($userId);

            if ($sharedUser) {
                $sharedUser->notify(new TodoSharedNotification($todo, $permission));

                \Log::info('Notificação de compartilhamento enviada', [
                    'shared_user_id' => $userId,
                    'todo_id' => $todo->id,
                    'permission' => $permission,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar notificação de compartilhamento', [
                'error' => $e->getMessage(),
                'shared_user_id' => $userId,
                'todo_id' => $todo->id,
                'permission' => $permission,
            ]);
        }
    }
}
