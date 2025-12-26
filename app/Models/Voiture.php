<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    use HasFactory;

    protected $fillable = [
        'immatriculation',
        'marque',
        'modele',
        'annee',
        'couleur',
        'prix_journalier',
        'statut',
        'kilometrage',
        'categorie_id',
        'agence_id',
        'photo',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function images()
    {
        return $this->hasMany(VoitureImage::class);
    }
}
