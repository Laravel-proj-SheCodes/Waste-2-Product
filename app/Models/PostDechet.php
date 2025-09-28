<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostDechet extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre','description','type_post','categorie','quantite','unite_mesure',
        'etat','localisation','user_id','date_publication','statut','photos'
    ];

    protected $casts = [
        'date_publication' => 'datetime',
        'photos' => 'array',
    ];

       public function offreTrocs()
    {
        return $this->hasMany(OffreTroc::class, 'post_dechet_id');
    }
    public function user(){ return $this->belongsTo(User::class); }
    public function propositions(){ return $this->hasMany(Proposition::class, 'post_dechet_id');
    
    
    }
   
// Dans app/Models/PostDechet.php - ajouter cette relation
public function annonceMarketplace()
{
    return $this->hasOne(AnnonceMarketplace::class);
}
// Nouvelle relation pour les favoris
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'post_dechet_id', 'user_id')->withTimestamps();
    }
}
