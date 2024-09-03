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
        return ClientRepository::findByTelephone($telephone);
    }

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
