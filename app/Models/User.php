<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relacionamento com as tarefas do usuário.
     */
    public function todos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * Relacionamento many-to-many com tarefas compartilhadas com este usuário.
     */
    public function sharedTodos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Todo::class, 'todo_user')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Relacionamento com os comentários do usuário.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relacionamento com tarefas atribuídas a este usuário.
     */
    public function assignedTodos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Todo::class, 'assigned_to');
    }

    /**
     * Relacionamento com comentários que o usuário reagiu.
     */
    public function commentReactions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comment_user')
            ->withPivot('reaction')
            ->withTimestamps();
    }
}
