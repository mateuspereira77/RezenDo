<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Obter todas as notificações do usuário autenticado.
     */
    public function index(): JsonResponse
    {
        $notifications = Auth::user()->notifications()->latest()->take(20)->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
            'total' => $notifications->count(),
        ]);
    }

    /**
     * Marcar notificação como lida.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marcar todas as notificações como lidas.
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Todas as notificações foram marcadas como lidas.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Obter contador de notificações não lidas.
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
