<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;



/**
 * @OA\Info(
 *     title="My API Documentation",
 *     version="1.0.0",
 *     description="This is the API documentation for my Laravel application.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "nom", "prenom", "photo", "login", "role_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nom", type="string", example="John"),
 *     @OA\Property(property="prenom", type="string", example="Doe"),
 *     @OA\Property(property="photo", type="string", example="photos/photo.jpg"),
 *     @OA\Property(property="login", type="string", example="user@example.com"),
 *     @OA\Property(property="role_id", type="integer", example=1)
 * )
 */
class AuthController extends Controller
{

        /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"nom", "prenom", "photo", "login", "password"},
     *                 @OA\Property(property="nom", type="string", example="John"),
     *                 @OA\Property(property="prenom", type="string", example="Doe"),
     *                 @OA\Property(property="photo", type="string", example="photos/photo.jpg"),
     *                 @OA\Property(property="login", type="string", example="user@example.com"),
     *                 @OA\Property(property="password", type="string", example="password")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="your-jwt-token")
     *             ),
     *             @OA\Property(property="message", type="string", example="User Registered Successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Validation Error")
     *         )
     *     )
     * )
     */

    public function register(Request $request)
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
        'role_id' => ['required', 'integer'],
        'photo' => 'nullable|image|max:2048'  // Modifié de required à nullable
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 400,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation',
        ], 400);
    }

      // Check if the role is 'boutiquier' and the user is pre-registered
   /*    if ($request->role_id == 'boutiquier') {
        $existingUser = User::where('login', $request->login)->where('role_id', 'boutiquier')->first();
        if (!$existingUser) {
            return response()->json([
                'status' => 403,
                'data' => null,
                'message' => 'Vous n\'avez pas les autorisations nécessaires pour vous inscrire en tant que boutiquier.',
            ], 403);
        }
    } */



    // Create client
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
        'message' => 'Client créé avec succès',
    ], 200);
}


/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Authenticate a user and return a JWT token",
 *     operationId="loginUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"login", "password"},
 *                 @OA\Property(property="login", type="string", example="ramasecksd"),
 *                 @OA\Property(property="password", type="string", example="Passer@123")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="token", type="string", example="your-jwt-token")
 *             ),
 *             @OA\Property(property="message", type="string", example="Connexion réussie")
 *         )
 *     ),
 
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Identifiants incorrects")
 *         )
 *     )
 * )
 */

    public function login(Request $request){
        // Validation
        $validator = Validator::make($request->all(),[
            'login' => ['required','string','max:255'],
            'password' => ['required','string','min:5'],
        ]); 
        if ($validator->fails()) {
        return response()->json([
            'status' => 400,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation',
        ], 400);
    }

        if($validator->fails()){
            return response()->json([
               'status'=> 400,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation',
                ], 400);
        }
        $credentials = $request->only('login','password');

        if(!Auth::attempt($credentials)){
            return response()->json([
               'status'=> 401,
                'data' => null,
               'message' => 'Identifiants incorrects',
                ], 401);
        }
        $user = Auth::user();
        $tokenResult = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
           'status'=> 200,
            'data' => ['token' => $tokenResult],
            'message' => 'Connexion réussie',
        ],200);
    

    }


}
