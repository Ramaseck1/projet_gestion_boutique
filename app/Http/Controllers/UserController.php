<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(title="API de gestion des utilisateurs", version="1.0")
 */
class UserController extends Controller
{
  
/**
     * @OA\Get(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="List all users with optional filters",
     *     operationId="listUsers",
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter users by role",
     *         required=false,
     *         @OA\Schema(type="string", enum={"1", "2"})
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filter users by active status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="message", type="string", example="Liste des utilisateurs")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No users found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Aucun utilisateur trouvé")
     *         )
     *     )
     * )
     */
     
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



    /**
     * @OA\Get(
     *     path="/api/v1/liste",
     *     tags={"Users"},
     *     summary="List all users",
     *     operationId="listAllUsers",
     *     @OA\Response(
     *         response=200,
     *         description="List of all users",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="message", type="string", example="Liste des utilisateurs")
     *         )
     *     )
     * )
     */
    public function liste(){
        $users = User::all();
        return response()->json([
            'status' => 200,
            'data' => $users,
            'message' => 'Liste des utilisateurs',
        ], 200);
    }

/**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     operationId="createUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"nom", "prenom", "login", "password", "role_id"},
     *                 @OA\Property(property="nom", type="string", example="John"),
     *                 @OA\Property(property="prenom", type="string", example="Doe"),
     *                 @OA\Property(property="login", type="string", example="user@example.com"),
     *                 @OA\Property(property="password", type="string", example="Password123!"),
     *                 @OA\Property(property="role_id", type="string", example="1"),
     *                 @OA\Property(property="photo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", @OA\Property(property="user", ref="#/components/schemas/User")),
     *             @OA\Property(property="message", type="string", example="Utilisateur créé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="object", @OA\Property(property="errors", type="object", additionalProperties=true)),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     )
     * )
     */
    

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
