<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Voiture;
use App\Models\Agence;
use App\Models\Paiement;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'date_debut',
        'date_fin',
        'prix_total',
        'statut',
        'voiture_id',
        'agence_retrait_id',
        'agence_retour_id',
    ];

    protected $dates = [
        'date_debut',
        'date_fin',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }

    public function agenceRetrait()
    {
        return $this->belongsTo(Agence::class, 'agence_retrait_id');
    }

    public function agenceRetour()
    {
        return $this->belongsTo(Agence::class, 'agence_retour_id');
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }
}
