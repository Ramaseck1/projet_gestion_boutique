<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthenticationServiceInterface;


class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }


   

    public function register(Request $request)
{


 
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



 public function login(Request $request)
 {
     // Validation
     $validator = Validator::make($request->all(), [
         'login' => ['required', 'string', 'max:255'],
         'password' => ['required', 'string', 'min:5'],
     ]);

     if ($validator->fails()) {
         return response()->json([
             'status' => 400,
             'data' => $validator->errors(),
             'message' => 'Erreur de validation',
         ], 400);
     }

     $credentials = $request->only('login', 'password');

     return $this->authService->authenticate($credentials);
 }

 public function logout()
 {
     return $this->authService->logout();
 }

}
