<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'user_id',
        'photo_profil',
    ];

    protected $appends = ['photo_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo_profil ? \Illuminate\Support\Facades\Storage::url($this->photo_profil) : null;
    }
}
