<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropositionTransformation extends Model
{
    
    protected $fillable = ['proposition_id','transformateur_id','description','statut'];

    public function proposition()
    {
        return $this->belongsTo(Proposition::class);
    }

    public function transformateur()
    {
        return $this->belongsTo(User::class, 'transformateur_id');
    }

    public function processus()
    {
        return $this->hasOne(ProcessusTransformation::class, 'proposition_transformation_id');
    }
}
