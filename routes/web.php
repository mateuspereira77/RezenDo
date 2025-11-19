<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [TodoController::class, 'index'])->name('todos.index');
    Route::get('/minhas-tarefas', [TodoController::class, 'list'])->name('todos.list');
    Route::get('/calendario', [TodoController::class, 'calendar'])->name('todos.calendar');
    Route::get('/meu-historico', [TodoController::class, 'history'])->name('todos.history');
    Route::get('/todos/history/{id}', [TodoController::class, 'showHistory'])->where('id', '[0-9]+')->name('todos.showHistory');
    Route::get('/todos/{todo}', [TodoController::class, 'show'])->name('todos.show');
    Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])->name('todos.edit');

    Route::prefix('api/todos')->group(function () {
        Route::get('/', [TodoController::class, 'all'])->name('todos.all');
        Route::get('/by-date-range', [TodoController::class, 'getByDateRange'])->name('todos.byDateRange');
        Route::post('/', [TodoController::class, 'store'])->name('todos.store');
        Route::put('/{todo}', [TodoController::class, 'update'])->name('todos.update');
        Route::delete('/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
        Route::patch('/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
        Route::patch('/{todo}/priority', [TodoController::class, 'changePriority'])->name('todos.changePriority');
        
        // Rotas de histórico
        Route::get('/history/all', [TodoController::class, 'getHistory'])->name('todos.history.all');
        Route::post('/history/{id}/restore', [TodoController::class, 'restore'])->name('todos.history.restore');
        Route::delete('/history/{id}/force', [TodoController::class, 'forceDelete'])->name('todos.history.forceDelete');

        // Rotas de compartilhamento
        Route::get('/{todo}/shares', [\App\Http\Controllers\TodoShareController::class, 'index'])->name('todos.shares.index');
        Route::post('/{todo}/shares', [\App\Http\Controllers\TodoShareController::class, 'store'])->name('todos.shares.store');
        Route::put('/{todo}/shares/{userId}', [\App\Http\Controllers\TodoShareController::class, 'update'])->name('todos.shares.update');
        Route::delete('/{todo}/shares/{userId}', [\App\Http\Controllers\TodoShareController::class, 'destroy'])->name('todos.shares.destroy');

        // Rotas de comentários
        Route::get('/{todo}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('todos.comments.index');
        Route::get('/history/{id}/comments', [\App\Http\Controllers\CommentController::class, 'indexHistory'])->name('todos.history.comments.index');
        Route::post('/{todo}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('todos.comments.store');
        Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
        Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/{todo}/comments/{comment}/reply', [\App\Http\Controllers\CommentController::class, 'reply'])->name('comments.reply');
        Route::post('/{todo}/comments/{comment}/react', [\App\Http\Controllers\CommentController::class, 'react'])->name('comments.react');
    });

    // Rota para buscar usuários
    Route::get('/api/users/search', [\App\Http\Controllers\UserController::class, 'search'])->name('users.search');

    // Rotas de notificações
    Route::prefix('api/notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    });
});
