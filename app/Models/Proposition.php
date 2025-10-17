<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\ProposalReceived;
use App\Notifications\ProposalAccepted;
use Illuminate\Support\Facades\Auth;

class Proposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_dechet_id',
        'user_id',
        'description',
        'date_proposition',
        'statut',
    ];

    protected $casts = [
        'date_proposition' => 'date',
    ];

    /**
     * Hooks de modèle pour gérer les notifs et valeurs par défaut
     */
    protected static function booted(): void
    {
        // Valeurs par défaut à la création
        static::creating(function (Proposition $p) {
            if (empty($p->statut)) {
                $p->statut = 'en_attente';
            }
            if (empty($p->date_proposition)) {
                $p->date_proposition = now();
            }
        });

        // Quand une proposition est créée -> notifier le propriétaire du post
        static::created(function (Proposition $p) {
            // Charger le post + l'owner + l'auteur si manquants
            $p->loadMissing('postDechet.user', 'user');

            $owner = $p->postDechet?->user;
            if (!$owner) {
                return;
            }

            // Ne pas notifier si on propose sur son propre post
            if ((int) $owner->id === (int) $p->user_id) {
                return;
            }

            $owner->notify(new ProposalReceived(
                postId:        (int) $p->post_dechet_id,
                propositionId: (int) $p->id,
                senderName:    $p->user?->name ?? 'Utilisateur',
                postTitle:     $p->postDechet->titre ?? 'Post'
            ));
        });

        // Quand une proposition passe à "acceptee" -> notifier l'auteur
        static::updated(function (Proposition $p) {
            if ($p->wasChanged('statut') && $p->statut === 'acceptee') {
                $p->loadMissing('user', 'postDechet');
                $p->user?->notify(new ProposalAccepted(
                    propositionId: (int) $p->id,
                    postTitle:     $p->postDechet->titre ?? 'Post'
                ));
            }
        });
    }

    /* =========================
     | Relations
     * ========================= */

    public function postDechet()
    {
        return $this->belongsTo(PostDechet::class, 'post_dechet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propositionTransformation()
    {
        return $this->hasOne(PropositionTransformation::class);
    }
}
