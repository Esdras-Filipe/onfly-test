<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['status'  => false, 'message' => 'Credenciais inválidas.'], 401);
        }

        return response()->json(['status' => true, 'token' => $token, 'type' => 'bearer']);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['status' => true, 'message' => 'Logout realizado com sucesso.']);
    }
}
