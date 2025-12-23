<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_permis',
        'adresse',
        'telephone',
        'date_naissance',
        'photo_profil',
    ];

    protected $appends = ['photo_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo_profil ? Storage::url($this->photo_profil) : null;
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
