<?php
namespace App\Services;

interface ClientService
{
    public function getAllClients();
    public function getClientsByAccounts($hasAccounts);
    public function getClientsByStatus($activeStatus);
    public function findByTelephone($telephone);
    public function createClient($data, $authUser);
    public function getClientById($id);
    public function getClientDebts($id);
    public function getClientWithUser($id);
}
