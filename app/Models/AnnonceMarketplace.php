<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/AnnonceMarketplace.php
class AnnonceMarketplace extends Model
{
    protected $fillable = ['post_dechet_id', 'prix', 'statut_annonce'];
    
    protected $casts = [
        'prix' => 'decimal:2',
    ];

    public function postDechet()
    {
        return $this->belongsTo(PostDechet::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
