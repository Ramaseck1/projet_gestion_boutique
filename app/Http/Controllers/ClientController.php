<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Assurez-vous d'importer le facade Log


class ClientController extends Controller
{
 
    public function index()
    {
        $clients=Client::all();

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


public function searchByTelephone(Request $request)
{
    $validator = Validator::make($request->all(), [
        'telephone' => ['required', 'string', 'regex:/^(77|78|70|76)\d{6}$/'],
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
    public function store(Request $request)
    {

        $authUser = Auth::user();
        // Vérifie si l'utilisateur authentifié est un boutiquier
        if ($authUser->role_id !== 2) {
            return response()->json([
                'status' => 403,
                'data' => null,
                'message' => 'Vous n\'avez pas les autorisations nécessaires pour ajouter un article',
            ], 403);
        }
        
      /*   Log::info('Données de la requête:', ['input' => $request->all()]);
        Log::info('Téléphone:', ['input' => $request->telephone]); */

        // Validation pour les données du client
        $validator = Validator::make($request->all(), [
            'surname' => ['required', 'string', 'max:255', 'unique:clients'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'unique:clients', 'regex:/^(77|78|70|76)\d{6}$/'],
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
        $client = Client::create([
            'surname' => $request->surname,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
        ]);

        // Si des informations utilisateur sont fournies
        if ($request->has('user')) {
            $userData = $request->input('user');

            // Création du compte utilisateur
            $user = new User();
            $user->nom = $client->surname; // Utiliser le même nom que le client ou une autre logique
            $user->prenom = ''; // Vous pouvez définir d'autres champs selon vos besoins
            $user->login = $userData['login'];
            $user->password = Hash::make($userData['password']);
            $user->role_id = 3; // Assigner un rôle de client ou selon votre logique
            $user->save();

            // Associer l'utilisateur au client
            $client->user_id = $user->id;
            $client->save();
        }

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
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
