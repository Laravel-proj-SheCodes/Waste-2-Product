<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Commande.php
class Commande extends Model
{
    protected $fillable = [
        'annonce_marketplace_id', 'user_id', 'quantite', 
        'prix_total', 'statut_commande', 'date_commande'
    ];
    
    protected $casts = [
        'prix_total' => 'decimal:2',
        'date_commande' => 'datetime',
    ];

    public function annonceMarketplace()
    {
        return $this->belongsTo(AnnonceMarketplace::class);
    }

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation pour obtenir le vendeur
    public function vendeur()
    {
        return $this->hasOneThrough(
            User::class, 
            AnnonceMarketplace::class, 
            'id', 'id', 
            'annonce_marketplace_id', 'post_dechet_id'
        )->join('post_dechets', 'annonce_marketplaces.post_dechet_id', '=', 'post_dechets.id');
    }

}
