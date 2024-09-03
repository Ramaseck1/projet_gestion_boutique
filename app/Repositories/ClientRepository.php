<?php
namespace App\Repositories;

interface ClientRepository
{
    public function getAll();
    public function getByAccounts($hasAccounts);
    public function getByStatus($activeStatus);
    public function findByTelephone($telephone);
    public function createClient($data, $authUser);
    public function getById($id);
    public function getDebts($id);
    public function getWithUser($id);
}
