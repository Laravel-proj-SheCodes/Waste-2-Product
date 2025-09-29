<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessusTransformation extends Model
{
    protected $fillable = ['proposition_transformation_id','dechet_entrant_id','duree_estimee','cout','equipements','statut'];

    public function propositionTransformation()
    {
        return $this->belongsTo(PropositionTransformation::class, 'proposition_transformation_id');
    }

    public function dechetEntrant()
    {
        return $this->belongsTo(PostDechet::class, 'dechet_entrant_id');
    }

    public function produits()
    {
        return $this->hasMany(ProduitTransforme::class, 'processus_id');
    }
}
