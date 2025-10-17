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
        User::class,              // 1. Modèle Final (le vendeur)
        AnnonceMarketplace::class, // 2. Modèle Intermédiaire (pour aller de Commande à PostDechet)
        'id',                     // 3. Clé étrangère sur la table Intermédiaire (annonce_marketplaces.id)
        'id',                     // 4. Clé étrangère sur la table Finale (users.id)
        'annonce_marketplace_id', // 5. Clé locale sur la table Actuelle (commandes.annonce_marketplace_id)
        'post_dechet_id'          // 6. Clé étrangère sur la table Intermédiaire (annonce_marketplaces.post_dechet_id)
    );
}

public function getVendeurAttribute()
{
    // Accès: Commande -> AnnonceMarketplace -> PostDechet -> User
    return $this->annonceMarketplace->postDechet->user ?? null;
}



}
