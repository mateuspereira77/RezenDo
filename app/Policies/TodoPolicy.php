<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Todo $todo): bool
    {
        // Pode visualizar se for o dono, se a tarefa foi atribuída a ele, ou se foi compartilhada com ele
        return $user->id === $todo->user_id
            || $user->id === $todo->assigned_to
            || $todo->isSharedWith($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Todo $todo): bool
    {
        // Pode editar se for o dono ou se tiver permissão de escrita na tarefa compartilhada
        return $todo->hasWritePermission($user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Todo $todo): bool
    {
        // Apenas o dono pode excluir
        return $user->id === $todo->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Todo $todo): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Todo $todo): bool
    {
        return false;
    }
}
