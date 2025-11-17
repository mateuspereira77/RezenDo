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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('text', 200)->comment('Título da tarefa (máximo 200 caracteres)');
            $table->text('description')->nullable()->comment('Descrição detalhada da tarefa (máximo 500 caracteres)');
            $table->boolean('completed')->default(false)->comment('Status de conclusão da tarefa');
            $table->enum('priority', ['simple', 'medium', 'urgent'])->default('simple')->comment('Nível de prioridade da tarefa');
            $table->string('day')->nullable()->comment('Dia da semana (campo legado)');
            $table->date('date')->nullable()->comment('Data específica da tarefa');
            $table->timestamps();

            // Índices para melhorar performance nas consultas mais comuns
            $table->index('completed', 'todos_completed_index')->comment('Índice para filtros por status de conclusão');
            $table->index('priority', 'todos_priority_index')->comment('Índice para ordenação por prioridade');
            $table->index('date', 'todos_date_index')->comment('Índice para filtros e ordenação por data');
            $table->index('created_at', 'todos_created_at_index')->comment('Índice para ordenação por data de criação');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
