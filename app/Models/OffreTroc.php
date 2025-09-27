<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffreTroc extends Model
{
    protected $table = 'offre_trocs';

    protected $fillable = [
        'categorie', 'quantite', 'unite_mesure', 'etat', 'localisation',
        'photos', 'description', 'user_id', 'post_dechet_id', 'status'
    ];

    protected $casts = [
        'photos' => 'array',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postDechet()
    {
        return $this->belongsTo(PostDechet::class, 'post_dechet_id');
    }

    public function transactionTroc()
    {
        return $this->hasOne(TransactionTroc::class, 'offre_troc_id'); // Lien vers TransactionTroc si acceptée
    }
}