<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;


class AuthentificationSanctum implements AuthenticationServiceInterface
{
    public function authenticate(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 401,
                'message' => 'Identifiants incorrects',
            ], 401);
        }

        $user = Auth::user();
        $tokenResult = $user->createToken('Sanctum Token')->plainTextToken;
        

        return response()->json([
            'status' => 200,
            'data' => ['token' => $tokenResult],
            'message' => 'Connexion réussie',
        ], 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Déconnexion réussie',
        ], 200);
    }
}
