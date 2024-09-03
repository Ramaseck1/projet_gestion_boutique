<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'date',
        'montant',
        'montant_du',
        'montant_restant',
    ];

    /**
     * Définir la relation avec le modèle Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
