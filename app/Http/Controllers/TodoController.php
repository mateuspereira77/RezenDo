<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function edit(Todo $todo): View
    {
        return view('todos.edit', ['todo' => $todo]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'priority' => ['required', 'in:simple,medium,urgent'],
            'day' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
        ]);

        $todo = Todo::create($validated);

        return response()->json($todo, 201);
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'completed' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', 'in:simple,medium,urgent'],
            'day' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
        ]);

        // Se date vier como string vazia, definir como null explicitamente
        if (isset($validated['date']) && $validated['date'] === '') {
            $validated['date'] = null;
        }
        
        // Sempre atualizar, mesmo que date seja null (para permitir remover a data)
        $todo->update($validated);

        // Recarregar o modelo para garantir que temos os dados atualizados
        $todo->refresh();

        return response()->json($todo);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso']);
    }

    public function toggle(Todo $todo): JsonResponse
    {
        $todo->toggleCompletion();

        return response()->json($todo);
    }

    public function changePriority(Todo $todo): JsonResponse
    {
        $priorities = ['simple', 'medium', 'urgent'];
        $currentIndex = array_search($todo->priority, $priorities);
        $nextIndex = ($currentIndex + 1) % count($priorities);
        $todo->update(['priority' => $priorities[$nextIndex]]);

        return response()->json($todo);
    }

    public function all(): JsonResponse
    {
        $todos = Todo::orderByPriority()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($todos);
    }
}
