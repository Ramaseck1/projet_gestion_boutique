<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Facades\ClientServiceFacade as ClientServiceFacade;
use Illuminate\Support\Facades\Log; // Assurez-vous d'importer le facade Log



class ClientController extends Controller
{

   

 
 
    public function index()
    {
        $clients= ClientServiceFacade::getAllClients();

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

   
    public function indexWithAccounts(Request $request)
{
         $hasAccounts = $request->query('comptes') === 'oui';
        $clients = ClientServiceFacade::getClientsByAccounts($hasAccounts);

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



public function indexActiveStatus(Request $request)
{
    $activeStatus = $request->query('active') === 'oui';
    $clients = ClientServiceFacade::getClientsByStatus($activeStatus);



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

    $client = ClientServiceFacade::findByTelephone($request->telephone);

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
        $client = ClientServiceFacade::createClient($request->all(), $authUser);



    
        return response()->json([
            'status' => 201,
            'data' => $client,
            'message' => 'Client enregistré avec succès',
        ], 201);
    }
    
    /**
     * Display the specified resource.
     */


  

    public function show($id)
    {
        $client = ClientServiceFacade::getClientById($id);    
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



   
    public function listDettes($id)
    {
        $client = ClientServiceFacade::getClientDebts($id);

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


  

    public function showUser($id)
    {
        $client = ClientServiceFacade::getClientWithUser($id);
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
