<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
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

    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = Todo::create($request->validated());

        return response()->json($todo, 201);
    }

    public function update(UpdateTodoRequest $request, Todo $todo): JsonResponse
    {
        $todo->update($request->validated());

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

    public function calendar(): View
    {
        return view('todos.calendar');
    }

    public function getByDateRange(): JsonResponse
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');

        $query = Todo::query()->whereNotNull('date');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $todos = $query->orderBy('date', 'asc')
            ->orderByPriority()
            ->get();

        return response()->json($todos);
    }
}
