<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_id',
        'user_id',
        'content',
        'parent_id',
    ];

    /**
     * Relacionamento com a tarefa.
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    /**
     * Relacionamento com o usuário que fez o comentário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o comentário pai (para respostas).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Relacionamento com as respostas do comentário.
     */
    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Relacionamento com usuários mencionados no comentário.
     */
    public function mentions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_mentions')
            ->withTimestamps();
    }

    /**
     * Relacionamento com usuários que reagiram ao comentário.
     */
    public function reactions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_user')
            ->withPivot('reaction')
            ->withTimestamps();
    }

    /**
     * Verificar se o usuário deu like no comentário.
     */
    public function isLikedBy(int $userId): bool
    {
        return \DB::table('comment_user')
            ->where('comment_id', $this->id)
            ->where('user_id', $userId)
            ->where('reaction', 'like')
            ->exists();
    }

    /**
     * Verificar se o usuário deu dislike no comentário.
     */
    public function isDislikedBy(int $userId): bool
    {
        return \DB::table('comment_user')
            ->where('comment_id', $this->id)
            ->where('user_id', $userId)
            ->where('reaction', 'dislike')
            ->exists();
    }

    /**
     * Obter contagem de likes.
     */
    public function getLikesCountAttribute(): int
    {
        return \DB::table('comment_user')
            ->where('comment_id', $this->id)
            ->where('reaction', 'like')
            ->count();
    }

    /**
     * Obter contagem de dislikes.
     */
    public function getDislikesCountAttribute(): int
    {
        return \DB::table('comment_user')
            ->where('comment_id', $this->id)
            ->where('reaction', 'dislike')
            ->count();
    }
}
