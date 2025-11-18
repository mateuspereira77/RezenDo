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
        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->index('assigned_to', 'todos_assigned_to_index')->comment('Índice para filtros por responsável');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropIndex('todos_assigned_to_index');
            $table->dropColumn('assigned_to');
        });
    }
};
