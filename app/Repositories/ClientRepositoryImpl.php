<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class ClientRepositoryImpl implements ClientRepository
{
    public function getAll()
    {
        return Client::all();
    }

    public function getByAccounts($hasAccounts)
    {
        return Client::whereHas('user', function ($query) use ($hasAccounts) {
            if ($hasAccounts) {
                $query->whereNotNull('id');
            } else {
                $query->whereNull('id');
            }
        })->get();
    }



    public function getByStatus($activeStatus)
    {
        return Client::whereHas('user', function ($query) use ($activeStatus) {
            $query->where('active', $activeStatus);
        })->get();
    }

    public function findByTelephone($telephone)
    {
        return Client::where('telephone', $telephone)->first(); //ON DOIT PLUS UTILISER WHERE on doit mettre le where dans scope 
    }

    

    public function createClient($data, $authUser)
    {
        // Création du client avec association à un utilisateur, gérée ici
        $client = new Client();
        $client->surname = $data['surname'];
        $client->address = $data['adresse'];
        $client->telephone = $data['telephone'];

        if (isset($data['user'])) {
            $user = new User();
            $user->nom = $client->surname;
            $user->prenom = '';
            $user->login = $data['user']['login'];
            $user->password = Hash::make($data['user']['password']);
            $user->role_id = 3;
            $user->save();
            $client->user_id = $user->id;
        } else {
            $client->user_id = $authUser->id;
        }

        $client->save();

        return $client;
    }

    public function getById($id)
    {
        return Client::find($id);
    }

    public function getDebts($id)
    {
        return Client::with('dettes')->find($id);
    }

    public function getWithUser($id)
    {
        return Client::with('user')->find($id);
    }
}
