<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Models\Voiture;
use App\Models\Reservation;

class Agence extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'ville',
        'logo',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function voitures()
    {
        return $this->hasMany(Voiture::class);
    }

    public function reservationsRetrait()
    {
        return $this->hasMany(Reservation::class, 'agence_retrait_id');
    }

    public function reservationsRetour()
    {
        return $this->hasMany(Reservation::class, 'agence_retour_id');
    }
}
