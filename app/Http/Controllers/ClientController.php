<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use App\Services\PhotoServiceInterface;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Facades\ClientServiceFacade as ClientServiceFacade;
use Illuminate\Support\Facades\Log; // Assurez-vous d'importer le facade Log
use App\Facades\UploadFacade;
use App\Facades\QrCodeServiceFacade;
use Illuminate\Support\Facades\Mail;
use App\Services\Base64ImageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\PngImageRenderer;
use BaconQrCode\Renderer\Image\RendererStyle\RendererStyleOptions;
use BaconQrCode\Renderer\Image\RendererOptions;
use BaconQrCode\Renderer\Image\Png;

 







class ClientController extends Controller
{

    protected $photoService;


    public function __construct(PhotoServiceInterface $photoService)
    {
        $this->photoService = $photoService;

    }
 
    public function index()
    {
        $clients= ClientServiceFacade::getAllClients();
        return response()->json($clients, $clients->isEmpty() ? 201 : 201);

        
    }

   
    public function indexWithAccounts(Request $request)
{
         $hasAccounts = $request->query('comptes') === 'oui';
        $client= ClientServiceFacade::getClientsByAccounts($hasAccounts);
        return response()->json($client, $client->isEmpty() ? 201 : 202);


    
}



public function indexActiveStatus(Request $request)
{
    $activeStatus = $request->query('active') === 'oui';
    $clien = ClientServiceFacade::getClientsByStatus($activeStatus);

    return response()->json($clien, $clien->isEmpty() ? 201 : 202);




 
}
public function searchByTelephone(Request $request)
{
    $validator = Validator::make($request->all(), [
        'telephone' => ['required', 'string', 'max:15'],
        'photo' => ['nullable', 'string'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 411,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation',
        ], 411);
    }

    // Convertir et stocker la photo en base64 si elle est fournie

    $clientss = ClientServiceFacade::findByTelephone($request->telephone);

    // Convertir et stocker la photo en base64
     if ($request->hasFile('photo')) {
        $base64Photo = $this->photoService->convertAndStorePhoto($request->file('photo'));
        $clientss->photo = $base64Photo;
    }
       


    return response()->json($clientss, $clientss? 201 : 201);



}

    
public function getClientCard($id)
     {
         $client = Client::find($id);
     
         if (!$client) {
             return response()->json(['error' => 'Client not found'], 404);
         }
     
         $qr_code_path = asset('storage/qrcodes/' . $client->user_id . '.png');
     
         // Envoyer l'email avec la carte de fidélité
         Mail::to($client->user->email)->send(new LoyaltyCardMail($client, $qr_code_path));
     
         return view('qrcode', [
             'client' => $client,
             'qr_code_url' => $qr_code_path
         ]);
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
             'user.photo' => ['required', 'image', 'mimes:png,jpeg,jpg,svg', 'max:4096'],
             'user.nom' => ['required', 'string', 'max:255'],
             'user.prenom' => ['required', 'string', 'max:255'],
             

         ]);
     
         if ($validator->fails()) {
             return response()->json([
                 'status' => 411,
                 'data' => $validator->errors(),
                 'message' => 'Erreur de validation',
             ], 411);
         }
             // Traitement du fichier photo
             $clientcreate = ClientServiceFacade::createClient($request->all(), $authUser);

        // Upload image avec base64
        if ($request->hasFile('user.photo')) {
            $base64Photo = $this->photoService->convertAndStorePhoto($request->file('user.photo'));
            $clientcreate->photo = $base64Photo;
        }

     
 // Génération du QR code avec les données du client
 $clientData = [
    'id' => $clientcreate->id,
    'nom' => $clientcreate->user->nom,
    'prenom' => $clientcreate->user->prenom,
    'telephone' => $clientcreate->telephone,
    'adresse' => $clientcreate->adresse,
]; 

 // Configuration du renderer pour GD
 $renderer = new PngImageRenderer(
    new RendererStyleOptions(), // Vous pouvez ajuster les options ici si nécessaire
    new PngImageRenderer() // Utilisez cette classe pour le rendu PNG
);

$writer = new Writer($renderer);

// Génération du QR code en format PNG
$qrCode = $writer->writeString(json_encode($clientData));

// Assurez-vous que le répertoire existe
$qrCodeDirectory = storage_path('app/public/qrcodes');
if (!is_dir($qrCodeDirectory)) {
    mkdir($qrCodeDirectory, 0755, true); // Crée le répertoire avec les permissions appropriées
}

// Sauvegarde de l'image QR code
$qrCodePath = 'qrcodes/client_' . $clientcreate->id . '.png';
Storage::disk('public')->put($qrCodePath, $qrCode);

// Associez le chemin du QR code au client ou faites ce que vous devez faire avec
$clientcreate->qr_code = $qrCodePath;
        $clientcreate->save();
     
    return response()->json($clientcreate, $clientcreate? 201 : 201);

     }
     
    /**
     * Display the specified resource.
     */


  

    public function show($id)
    {
        $client = ClientServiceFacade::getClientById($id);    
        return response()->json($client, $client? 203 : 203);

    }



   
    public function listDettes($id)
    {
        $client = ClientServiceFacade::getClientDebts($id);

        return response()->json($client, $client? 203 : 203);

        
    }


  

    public function showUser($id)
    {
        $client = ClientServiceFacade::getClientWithUser($id);
        return response()->json($client, $client? 203 : 203);

       
    }

}
