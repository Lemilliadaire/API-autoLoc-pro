<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
