<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Listar comentários de uma tarefa.
     */
    public function index(Todo $todo): JsonResponse
    {
        // Verificar se o usuário tem acesso à tarefa
        $this->authorize('view', $todo);

        $userId = auth()->id();

        $comments = $todo->comments()
            ->whereNull('parent_id') // Apenas comentários principais (não respostas)
            ->with(['user:id,name,email'])
            ->get()
            ->map(function ($comment) {
                // Carregar respostas recursivamente
                $comment->replies = $this->loadRepliesRecursively($comment);
                // Calcular a data da última atividade (comentário principal ou qualquer resposta)
                $comment->last_activity_at = $this->getLastActivityDate($comment);

                return $comment;
            })
            ->sortByDesc('last_activity_at')
            ->values();

        return response()->json($comments);
    }

    /**
     * Criar um novo comentário.
     */
    public function store(StoreCommentRequest $request, Todo $todo): JsonResponse
    {
        // Verificar se o usuário tem acesso à tarefa
        $this->authorize('view', $todo);

        $comment = Comment::create([
            'todo_id' => $todo->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->input('parent_id'), // Pode ser null para comentário principal
        ]);

        $comment->load('user:id,name,email');

        return response()->json($comment, 201);
    }

    /**
     * Responder a um comentário.
     */
    public function reply(StoreCommentRequest $request, Todo $todo, Comment $comment): JsonResponse
    {
        // Verificar se o comentário pertence à tarefa
        if ($comment->todo_id !== $todo->id) {
            return response()->json(['message' => 'Comentário não pertence a esta tarefa.'], 404);
        }

        // Verificar se o usuário tem acesso à tarefa
        $this->authorize('view', $todo);

        $reply = Comment::create([
            'todo_id' => $todo->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $comment->id,
        ]);

        $reply->load('user:id,name,email');

        return response()->json($reply, 201);
    }

    /**
     * Dar like ou dislike em um comentário.
     */
    public function react(Comment $comment): JsonResponse
    {
        // Verificar se o usuário tem acesso à tarefa
        $todo = $comment->todo;
        $this->authorize('view', $todo);

        $userId = auth()->id();
        $reaction = request()->input('reaction', 'like'); // 'like' ou 'dislike'

        if (! in_array($reaction, ['like', 'dislike'])) {
            return response()->json(['message' => 'Reação inválida.'], 400);
        }

        // Verificar se o usuário já reagiu
        $existingReaction = $comment->reactions()
            ->where('users.id', $userId)
            ->first();

        if ($existingReaction) {
            // Se já reagiu com a mesma reação, remover
            if ($existingReaction->pivot->reaction === $reaction) {
                $comment->reactions()->detach($userId);
                $action = 'removido';
            } else {
                // Se reagiu diferente, atualizar
                $comment->reactions()->updateExistingPivot($userId, ['reaction' => $reaction]);
                $action = 'atualizado';
            }
        } else {
            // Adicionar nova reação
            $comment->reactions()->attach($userId, ['reaction' => $reaction]);
            $action = 'adicionado';
        }

        $comment->refresh();
        $comment->load('user:id,name,email');
        $comment->is_liked = $comment->isLikedBy($userId);
        $comment->is_disliked = $comment->isDislikedBy($userId);
        $comment->likes_count = $comment->getLikesCountAttribute();
        $comment->dislikes_count = $comment->getDislikesCountAttribute();

        return response()->json([
            'message' => "Reação {$action} com sucesso.",
            'comment' => $comment,
        ]);
    }

    /**
     * Atualizar um comentário.
     */
    public function update(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        // Verificar se o usuário é o autor do comentário
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Você só pode editar seus próprios comentários.'], 403);
        }

        $comment->update([
            'content' => $request->content,
        ]);

        $comment->load('user:id,name,email');

        return response()->json($comment);
    }

    /**
     * Excluir um comentário.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        // Verificar se o usuário é o autor do comentário ou o dono da tarefa
        $todo = $comment->todo;
        if ($comment->user_id !== auth()->id() && $todo->user_id !== auth()->id()) {
            return response()->json(['message' => 'Você não tem permissão para excluir este comentário.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentário excluído com sucesso.']);
    }

    /**
     * Carregar respostas recursivamente.
     */
    private function loadRepliesRecursively(Comment $comment): \Illuminate\Database\Eloquent\Collection
    {
        $replies = $comment->replies()
            ->with('user:id,name,email')
            ->orderBy('created_at', 'asc')
            ->get();

        // Carregar respostas de cada resposta recursivamente
        foreach ($replies as $reply) {
            $reply->replies = $this->loadRepliesRecursively($reply);
        }

        return $replies;
    }

    /**
     * Obter a data da última atividade em um comentário e suas respostas.
     */
    private function getLastActivityDate(Comment $comment): \Carbon\Carbon
    {
        $latestDate = $comment->created_at;

        // Verificar todas as respostas recursivamente
        foreach ($comment->replies as $reply) {
            $replyDate = $this->getLastActivityDate($reply);
            if ($replyDate->gt($latestDate)) {
                $latestDate = $replyDate;
            }
        }

        return $latestDate;
    }
}
