<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitTransforme extends Model
{
     protected $table = 'produit_transformes';
    protected $fillable = ['processus_id','nom_produit','description','quantite_produite','valeur_ajoutee','prix_vente','photo'];

    public function processus()
    {
        return $this->belongsTo(ProcessusTransformation::class, 'processus_id');
    }
}
