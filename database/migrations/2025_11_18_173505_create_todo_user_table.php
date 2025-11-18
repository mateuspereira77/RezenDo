<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todo_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission', ['read', 'write'])->default('read')->comment('Permissão: read (apenas visualizar) ou write (pode editar)');
            $table->timestamps();

            // Garantir que um usuário não pode ter a mesma tarefa compartilhada duas vezes
            $table->unique(['todo_id', 'user_id'], 'todo_user_unique');
            $table->index('todo_id', 'todo_user_todo_id_index');
            $table->index('user_id', 'todo_user_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_user');
    }
};
