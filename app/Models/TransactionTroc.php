<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTroc extends Model
{
    use HasFactory;

    protected $table = 'transaction_trocs';

    protected $fillable = [
        'offre_troc_id',
        'utilisateur_acceptant_id',
        'date_accord',
        'statut_livraison',
        'evaluation_mutuelle',
    ];

    // Ajout du cast pour traiter date_accord comme une date (Carbon)
    protected $casts = [
        'date_accord' => 'datetime',
    ];

    // Relations
    public function offreTroc()
    {
        return $this->belongsTo(OffreTroc::class);
    }

    public function utilisateurAcceptant()
    {
        return $this->belongsTo(User::class, 'utilisateur_acceptant_id');
    }
}