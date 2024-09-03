<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Dette;

class DetteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assurez-vous d'avoir des clients existants pour les associer aux dettes
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->info('Aucun client trouvé, assurez-vous d\'avoir des clients dans la base de données avant de lancer ce seeder.');
            return;
        }

        foreach ($clients as $client) {
            Dette::create([
                'client_id' => $client->id,
                'date' => now(),
                'montant' => rand(100, 1000), // Valeur aléatoire pour le montant total de la dette
                'montant_du' => rand(50, 500), // Valeur aléatoire pour le montant dû
                'montant_restant' => rand(50, 500), // Valeur aléatoire pour le montant restant à payer
            ]);
        }

        $this->command->info('Dettes ajoutées avec succès.');
    }
}
