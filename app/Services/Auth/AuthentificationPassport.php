<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;

class AuthentificationPassport implements AuthenticationServiceInterface
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
        $tokenResult = $user->createToken('Personal Access Token')->accessToken;
        

        return response()->json([
            'status' => 200,
            'data' => ['token' => $tokenResult],
            'message' => 'Connexion réussie',
        ], 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->token()->revoke();

        return response()->json([
            'status' => 200,
            'message' => 'Déconnexion réussie',
        ], 200);
    }}
