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

    public function user(){ return $this->belongsTo(User::class); }
    public function propositions(){ return $this->hasMany(Proposition::class, 'post_dechet_id'); }

   // public function propositions()
   //{
    //return $this->hasMany(Proposition::class);
    //}

public function processusTransformations()
{
    return $this->hasManyThrough(
        ProcessusTransformation::class,
        PropositionTransformation::class,
        'proposition_id',              // local key on proposition_transformations -> links to proposition
        'dechet_entrant_id',           // local key on processus_transformations -> links to post_dechets
        'id',                          // local key on post_dechets
        'id'                           // local key on proposition_transformations
    );
}

}
