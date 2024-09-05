<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Facades\ClientRepositoryFascade as ClientRepository;
use App\Models\Client;

class ClientServiceImpl implements ClientService
{
  

    public function getAllClients()
    {
        return ClientRepository::getAll();
    }

    public function getClientsByAccounts($hasAccounts)
    {
        return ClientRepository::getByAccounts($hasAccounts);
    }

    public function getClientsByStatus($activeStatus)
    {
        return ClientRepository::getByStatus($activeStatus);
    }

    public function findByTelephone($telephone)
    {
        $client = ClientRepository::findByTelephone($telephone);
    
        // Assurez-vous que les informations du client incluent les détails de l'utilisateur
        if ($client && $client->user) {
            return [
                'client' => $client,
                'user' => $client->user,
                'photo' => $client->user->photo
            ];
        }
    
        return null;    }

    public function createClient($data, $authUser)
    {
        return ClientRepository::createClient($data, $authUser);
    }

    public function getClientById($id)
    {
        return ClientRepository::getById($id);
    }

    public function getClientDebts($id)
    {
        return ClientRepository::getDebts($id);
    }

    public function getClientWithUser($id)
    {
        return ClientRepository::getWithUser($id);
    }
}
