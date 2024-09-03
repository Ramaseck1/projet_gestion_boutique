<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Clé étrangère vers la table clients
            $table->date('date');
            $table->decimal('montant', 10, 2); // Montant total de la dette
            $table->decimal('montant_du', 10, 2)->default(0); // Montant dû
            $table->decimal('montant_restant', 10, 2)->default(0); // Montant restant à payer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dettes');
    }
};
