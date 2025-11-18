<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Buscar usuários por nome ou email.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        $usersQuery = User::where('id', '!=', auth()->id());

        if (strlen($query) >= 1) {
            $usersQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $users = $usersQuery
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(10)
            ->get();

        \Log::info('Busca de usuários', ['query' => $query, 'count' => $users->count()]);

        return response()->json($users);
    }
}
