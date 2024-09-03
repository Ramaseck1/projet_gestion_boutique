<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;



class UserController extends Controller
{
  

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->has('active')) {
            $isActive = $request->active === 'oui' ? 1 : 0;
            $query->where('active', $isActive);
        }

        $users = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $users->isEmpty() ? null : $users,
            'message' => $users->isEmpty() ? 'Aucun utilisateur trouvé' : 'Listess des utilisateurs',
        ], 200);
    }



 
    public function liste(){
        $users = User::all();
        return response()->json([
            'status' => 200,
            'data' => $users,
            'message' => 'Liste des utilisateurs',
        ], 200);
    }

    public function store(Request $request)
    {
    
    
      /*   // Vérifie si l'utilisateur est authentifié
            $authUser = Auth::user();
            // Vérifie si l'utilisateur authentifié est un boutiquier
            if ($authUser->role_id !== 'boutiquier') {
                return response()->json([
                    'status' => 403,
                    'data' => null,
                    'message' => 'Vous n\'avez pas les autorisations nécessaires pour créer un client',
                ], 403);
            } */
        // Validation
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'login' => ['required', 'string', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:5',
                'regex:/[a-z]/',              // au moins une lettre minuscule
                'regex:/[A-Z]/',              // au moins une lettre majuscule
                'regex:/[0-9]/',              // au moins un chiffre
                'regex:/[@$!%*#?&]/'          // au moins un caractère spécial
            ],
            'role_id' => 'required|string|in:1,2',
            'photo' => 'nullable|image|max:2048'  // Modifié de required à nullable
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation',
            ], 400);
        }
    

        
    
        // Create user
        $user = new User();
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->login = $request->login;
        $user->role_id = $request->role_id;
        $user->password = bcrypt($request->password);
    
        // Upload image
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = $path;
        }
    
        $user->save();
    
        return response()->json([
            'status' => 200,
            'data' => [
                'user' => $user,
            ],
            'message' => 'Utilisateur créé avec succès',
        ], 200);
    }


     

    //desactiver compte
    public function deactivate($id)
    {
        // Trouver l'utilisateur par ID
        $user = User::find($id);

        
        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json([
                'status' => 404,
                'data' => null,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        // Mettre à jour le statut de l'utilisateur à inactif (0)
        $user->active = 0;
        $user->save();

        return response()->json([
            'status' => 200,
            'data' => $user,
            'message' => 'Utilisateur désactivé avec succès',
        ], 200);
    }
}
