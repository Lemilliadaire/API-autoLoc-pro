<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class VoitureImage extends Model
{
    protected $fillable = [
        'voiture_id',
        'image_path',
        'type',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }
}
