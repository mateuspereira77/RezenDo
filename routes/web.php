<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodoController::class, 'index'])->name('todos.index');
Route::get('/minhas-tarefas', [TodoController::class, 'list'])->name('todos.list');
Route::get('/calendario', [TodoController::class, 'calendar'])->name('todos.calendar');
Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])->name('todos.edit');

Route::prefix('api/todos')->group(function () {
    Route::get('/', [TodoController::class, 'all'])->name('todos.all');
    Route::get('/by-date-range', [TodoController::class, 'getByDateRange'])->name('todos.byDateRange');
    Route::post('/', [TodoController::class, 'store'])->name('todos.store');
    Route::put('/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::delete('/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
    Route::patch('/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
    Route::patch('/{todo}/priority', [TodoController::class, 'changePriority'])->name('todos.changePriority');
});
