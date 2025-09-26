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