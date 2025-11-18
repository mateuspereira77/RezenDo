<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\CommentMentionedNotification;
use App\Notifications\TodoCommentedNotification;
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
            ->with(['user:id,name,email', 'mentions:id,name,email'])
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

        // Processar menções
        $this->processMentions($comment, $request->content);

        // Notificar o dono da tarefa (se não for o próprio autor do comentário)
        $this->notifyTodoOwner($comment, $todo);

        $comment->load(['user:id,name,email', 'mentions:id,name,email', 'todo:id,text']);

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

        // Processar menções
        $this->processMentions($reply, $request->content);

        // Notificar o dono da tarefa (se não for o próprio autor do comentário)
        $this->notifyTodoOwner($reply, $todo);

        $reply->load(['user:id,name,email', 'mentions:id,name,email', 'todo:id,text']);

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

        // Remover menções antigas e processar novas
        $comment->mentions()->detach();
        $this->processMentions($comment, $request->content);

        $comment->load(['user:id,name,email', 'mentions:id,name,email']);

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
            ->with(['user:id,name,email', 'mentions:id,name,email'])
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

    /**
     * Processar menções de usuários no conteúdo do comentário.
     */
    private function processMentions(Comment $comment, string $content): void
    {
        // Regex para encontrar menções no formato @nome
        // A estratégia é: quando o usuário seleciona do dropdown, o nome é inserido seguido de um espaço
        // Então precisamos capturar apenas o nome mencionado (até o próximo espaço ou fim de linha)
        // Mas permitir nomes compostos como "Mateus Pereira"

        // Primeiro, tentar capturar nomes compostos (até 3 palavras, parando quando encontrar espaço + palavra minúscula ou pontuação)
        preg_match_all('/@([A-ZÀ-ÿ][a-zà-ÿ]+(?:\s+[A-ZÀ-ÿ][a-zà-ÿ]+)*)/u', $content, $matches);

        // Se não encontrou nomes compostos, tentar apenas primeira palavra (nome simples)
        if (empty($matches[1])) {
            preg_match_all('/@([a-zA-ZÀ-ÿ]+)/u', $content, $matches);
        }

        // Limpar matches: remover espaços extras e normalizar
        $matches[1] = array_map(function ($match) {
            return trim($match);
        }, $matches[1] ?? []);

        \Log::info('Processando menções', [
            'comment_id' => $comment->id,
            'content' => $content,
            'matches' => $matches[1] ?? [],
        ]);

        if (empty($matches[1])) {
            \Log::info('Nenhuma menção encontrada no conteúdo');

            return;
        }

        $mentionedUserIds = [];
        $currentUserId = auth()->id();

        // Carregar relacionamento todo e user para as notificações
        $comment->load(['todo:id,text', 'user:id,name']);

        foreach ($matches[1] as $mention) {
            $mention = trim($mention);

            if (empty($mention)) {
                continue;
            }

            \Log::info('Buscando usuário para menção', [
                'mention' => $mention,
                'comment_id' => $comment->id,
            ]);

            // Buscar usuário por nome exato primeiro, depois por parte do nome
            $user = User::where('id', '!=', $currentUserId)
                ->where(function ($query) use ($mention) {
                    // Busca exata (case insensitive)
                    $query->whereRaw('LOWER(name) = ?', [strtolower($mention)])
                        // Ou busca parcial no início do nome
                        ->orWhereRaw('LOWER(name) LIKE ?', [strtolower($mention).'%'])
                        // Ou busca parcial no email
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%'.strtolower($mention).'%']);
                })
                ->first();

            if ($user) {
                \Log::info('Usuário encontrado para menção', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'mention' => $mention,
                ]);

                if (! in_array($user->id, $mentionedUserIds)) {
                    $mentionedUserIds[] = $user->id;
                    $comment->mentions()->attach($user->id);

                    \Log::info('Menção anexada ao comentário', [
                        'user_id' => $user->id,
                        'comment_id' => $comment->id,
                    ]);

                    // Enviar notificação apenas se não for o próprio autor
                    if ($user->id !== $currentUserId) {
                        try {
                            // Garantir que os relacionamentos estão carregados
                            if (! $comment->relationLoaded('todo')) {
                                $comment->load('todo:id,text');
                            }
                            if (! $comment->relationLoaded('user')) {
                                $comment->load('user:id,name');
                            }

                            $user->notify(new CommentMentionedNotification($comment));

                            \Log::info('Notificação de menção enviada com sucesso', [
                                'user_id' => $user->id,
                                'comment_id' => $comment->id,
                                'mention' => $mention,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Erro ao enviar notificação de menção', [
                                'error' => $e->getMessage(),
                                'user_id' => $user->id,
                                'comment_id' => $comment->id,
                                'mention' => $mention,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                    } else {
                        \Log::info('Notificação não enviada: usuário mencionado é o próprio autor', [
                            'user_id' => $user->id,
                            'comment_id' => $comment->id,
                        ]);
                    }
                }
            } else {
                \Log::warning('Usuário não encontrado para menção', [
                    'mention' => $mention,
                    'comment_id' => $comment->id,
                ]);
            }
        }
    }

    /**
     * Notificar o dono da tarefa quando alguém comenta.
     */
    private function notifyTodoOwner(Comment $comment, Todo $todo): void
    {
        $commentAuthorId = auth()->id();
        $todoOwnerId = $todo->user_id;

        // Não notificar se o autor do comentário for o dono da tarefa
        if ($commentAuthorId === $todoOwnerId) {
            return;
        }

        try {
            // Carregar relacionamentos necessários
            $comment->load(['user:id,name', 'todo:id,text']);
            $todoOwner = User::find($todoOwnerId);

            if ($todoOwner) {
                $todoOwner->notify(new TodoCommentedNotification($comment));

                \Log::info('Notificação de comentário enviada ao dono da tarefa', [
                    'todo_owner_id' => $todoOwnerId,
                    'comment_id' => $comment->id,
                    'todo_id' => $todo->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar notificação de comentário ao dono da tarefa', [
                'error' => $e->getMessage(),
                'todo_owner_id' => $todoOwnerId,
                'comment_id' => $comment->id,
                'todo_id' => $todo->id,
            ]);
        }
    }
}
