<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SessionController extends Controller
{
    public function store(Request $req)
    {
        $req->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $user = User::where('login', $req->login)->first();
        if (! $user || ! Hash::check($req->password, $user->password)) {
            return response()->json(['message' => 'Неверные данные'], 401);
        }

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Сессия завершена']);
    }
}
