<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Facades\ClientServiceFacade;
use Illuminate\Support\Facades\Log; // Assurez-vous d'importer le facade Log

/**
 * @OA\Tag(
 *     name="Client",
 *     description="Les opérations liées aux articles"
 * )
 */
/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     title="Client",
 *     description="Client schema",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID du client",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="surname",
 *         type="string",
 *         description="Nom de famille du client",
 *         example="Dupont"
 *     ),
 *     @OA\Property(
 *         property="adresse",
 *         type="string",
 *         description="Adresse du client",
 *         example="123 Rue Example"
 *     ),
 *     @OA\Property(
 *         property="telephone",
 *         type="string",
 *         description="Numéro de téléphone du client",
 *         example="0612345678"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="Informations de l'utilisateur associé",
 *         @OA\Property(
 *             property="login",
 *             type="string",
 *             example="client_login"
 *         ),
 *         @OA\Property(
 *             property="password",
 *             type="string",
 *             example="Password123!"
 *         )
 *     )
 * )
 */

class ClientController extends Controller
{

    protected $clientServiceFacade;

    public function __construct(ClientServiceFacade $clientServiceFacade)
    {
        $this->clientServiceFacade = $clientServiceFacade;
    }
 /**
 * @OA\Get(
 *     path="/apiv1/clients",
 *     tags={"Client"},
 *     summary="Récupère la liste de tous les clients",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des clients",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Client")),
 *             @OA\Property(property="message", type="string", example="Liste des clients")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun client trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Pas de clients")
 *         )
 *     )
 * )
 */
    public function index()
    {
        $clients= $this->clientServiceFacade->getAllClients();

        if ($clients->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas de clients',
            ], 200);
        }
    
        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients',
        ], 200);

    }

    /**
 * @OA\Get(
 *     path="/api/clients",
 *     tags={"Client"},
 *     summary="Récupère la liste des clients avec ou sans comptes",
 *     @OA\Parameter(
 *         name="comptes",
 *         in="query",
 *         description="Filtrer les clients par comptes. Utiliser 'oui' pour les clients avec comptes, 'non' pour sans comptes",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={"oui", "non"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des clients",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Client")),
 *             @OA\Property(property="message", type="string", example="Liste des clients")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Paramètre invalide",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Paramètre invalide")
 *         )
 *     )
 * )
 */

    public function indexWithAccounts(Request $request)
{
    $query = Client::query();

    if ($request->has('comptes')) {
        $comptes = $request->query('comptes');

        if ($comptes === 'oui') {
            $query->whereHas('user');
        } elseif ($comptes === 'non') {
            $query->doesntHave('user');
        } else {
            return response()->json([
                'status' => 400,
                'data' => null,
                'message' => 'Paramètre invalide',
            ], 400);
        }
    }

    
    $clients = $query->get();

    if ($clients->isEmpty()) {
        return response()->json([
            'status' => 200,
            'data' => null,
            'message' => 'Pas de clients',
        ], 200);
    }

    return response()->json([
        'status' => 200,
        'data' => $clients,
        'message' => 'Liste des clients',
    ], 200);
}

/**
 * @OA\Get(
 *     path="/api/clients/ActiveStatus",
 *     tags={"Client"},
 *     summary="Récupère la liste des clients en fonction du statut actif de leur compte",
 *     @OA\Parameter(
 *         name="active",
 *         in="query",
 *         description="Filtrer les clients par statut actif du compte. Utiliser 'oui' pour les clients actifs, 'non' pour inactifs",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={"oui", "non"}
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des clients",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Client")),
 *             @OA\Property(property="message", type="string", example="Liste des clients")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Paramètre invalide",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Paramètre invalide")
 *         )
 *     )
 * )
 */

public function indexActiveStatus(Request $request)
{
    $query = Client::query();

    if ($request->has('active')) {
        $active = $request->query('active');

        if ($active === 'oui') {
            $query->whereHas('user', function ($q) {
                $q->where('active', true);
            });
        } elseif ($active === 'non') {
            $query->whereHas('user', function ($q) {
                $q->where('active', false);
            });
        } else {
            return response()->json([
                'status' => 400,
                'data' => null,
                'message' => 'Paramètre invalide',
            ], 400);
        }
    }

    $clients = $query->get();

    if ($clients->isEmpty()) {
        return response()->json([
            'status' => 200,
            'data' => null,
            'message' => 'Pas de clients',
        ], 200);
    }

    return response()->json([
        'status' => 200,
        'data' => $clients,
        'message' => 'Liste des clients',
    ], 200);
}

/**
 * @OA\Post(
 *     path="/api/clients/telephone",
 *     tags={"Client"},
 *     summary="Recherche un client par numéro de téléphone",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="telephone", type="string", example="0612345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé ou non",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="object", ref="#/components/schemas/Client"),
 *             @OA\Property(property="message", type="string", example="Client trouvé")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Erreur de validation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=411),
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="message", type="string", example="Erreur de validation")
 *         )
 *     )
 * )
 */

public function searchByTelephone(Request $request)
{
    $validator = Validator::make($request->all(), [
        'telephone' => ['required', 'string', 'max:15'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 411,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation',
        ], 411);
    }

    $client = Client::where('telephone', $request->telephone)->first();

    if (!$client) {
        return response()->json([
            'status' => 200,
            'data' => null,
            'message' => 'Client non trouvé',
        ], 200);
    }

    return response()->json([
        'status' => 200,
        'data' => $client,
        'message' => 'Client trouvé',
    ], 200);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


     /**
 * @OA\Post(
 *     path="/api/v1/clients",
 *     tags={"Client"},
 *     summary="Crée un nouveau client",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="surname", type="string", example="Dupont"),
 *             @OA\Property(property="adresse", type="string", example="123 Rue Example"),
 *             @OA\Property(property="telephone", type="string", example="0612345678"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="login", type="string", example="client_login"),
 *                 @OA\Property(property="password", type="string", example="Password123!")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Client enregistré avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=201),
 *             @OA\Property(property="data", type="object", ref="#/components/schemas/Client"),
 *             @OA\Property(property="message", type="string", example="Client enregistré avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Vous n'avez pas les autorisations nécessaires pour ajouter un client")
 *         )
 *     ),
 *     @OA\Response(
 *         response=411,
 *         description="Erreur de validation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=411),
 *             @OA\Property(property="data", type="object"),
 *             @OA\Property(property="message", type="string", example="Erreur de validation")
 *         )
 *     )
 * )
 */
    public function store(Request $request)
    {
        $authUser = Auth::user();
    
        // Vérifie si l'utilisateur authentifié est un boutiquier
        if ($authUser->role_id !== 2) {
            return response()->json([
                'status' => 403,
                'data' => null,
                'message' => 'Vous n\'avez pas les autorisations nécessaires pour ajouter un client',
            ], 403);
        }
    
        // Validation pour les données du client
        $validator = Validator::make($request->all(), [
            'surname' => ['required', 'string', 'max:255', 'unique:clients'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'unique:clients', 'max:15'],
            'user.login' => ['sometimes', 'required', 'string', 'unique:users,login'],
            'user.password' => ['sometimes', 'required', 'string', 'min:5', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 411,
                'data' => $validator->errors(),
                'message' => 'Erreur de validation',
            ], 411);
        }
    
       // Création du client
$client = new Client();
$client->surname = $request->surname;
$client->address = $request->adresse; // Utilisez le nom correct de la colonne ici
$client->telephone = $request->telephone;

// Assurez-vous de gérer le user_id correctement comme précédemment
if ($request->has('user')) {
    $userData = $request->input('user');

    $user = new User();
    $user->nom = $client->surname;
    $user->prenom = '';
    $user->login = $userData['login'];
    $user->password = Hash::make($userData['password']);
    $user->role_id = 3;
    $user->save();

    $client->user_id = $user->id;
} else {
    $client->user_id = $authUser->id;
}

$client->save();
    
        return response()->json([
            'status' => 201,
            'data' => $client,
            'message' => 'Client enregistré avec succès',
        ], 201);
    }
    
    /**
     * Display the specified resource.
     */


     /**
 * @OA\Get(
 *     path="/api/v1/clients/{id}",
 *     tags={"Client"},
 *     summary="Affiche les détails d'un client spécifique",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du client",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="object", ref="#/components/schemas/Client"),
 *             @OA\Property(property="message", type="string", example="Client trouvé")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Client non trouvé")
 *         )
 *     )
 * )
 */

    public function show($id)
    {
        $client = Client::find($id);
    
        if (!$client) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé',
            ], 411);
        }
    
        return response()->json([
            'status' => 200,
            'data' => $client->makeHidden(['user']), // Cache l'information du compte utilisateur
            'message' => 'Client trouvé',
        ], 200);
    }


 /**
 * @OA\Get(
 *     path="/api/v1/clients/{id}/dettes",
 *     tags={"Dettes"},
 *     summary="Récupère la liste des dettes",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des dettes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Dette")
 *             ),
 *             @OA\Property(property="message", type="string", example="Liste des dettes récupérée avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur serveur",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Erreur serveur")
 *         )
 *     )
 * )
 */
    

 /**
 * @OA\Schema(
 *     schema="Dette",
 *     type="object",
 *     required={"id", "montant", "date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="montant", type="number", format="float", example=100.50),
 *     @OA\Property(property="date", type="string", format="date", example="2024-09-01"),
 *     @OA\Property(property="description", type="string", example="Une dette pour achat matériel")
 * )
 */

   
    public function listDettes($id)
    {
        $client = Client::with('dettes')->find($id);

        if (!$client) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => "Objet non trouvé"
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'client' => $client,
                'dettes' => $client->debts ?? null
            ],
            'message' => "client trouvé"
        ]);
    }


  

     /**
 * @OA\Get(
 *     path="/api/v1/clients/{id}/user" ,
 *     tags={"User"},
 *     summary="Récupère les détails d'un utilisateur spécifique",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de l'utilisateur",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", ref="#/components/schemas/User"),
 *             @OA\Property(property="message", type="string", example="Utilisateur trouvé")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Utilisateur non trouvé")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erreur serveur",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Erreur serveur")
 *         )
 *     )
 * )
 */
    public function showUser($id)
    {
        $client = Client::with('user')->find($id);

        if (!$client) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => "Objet non trouvé"
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'client' => $client,
                'user' => $client->user ?? null
            ],
            'message' => "client trouvé"
        ]);
    }

}
