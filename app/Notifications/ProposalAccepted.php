<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class ProposalAccepted extends Notification
{
    use Queueable;

    /**
     * @param int    $postId         ID du post (pour ouvrir la page front)
     * @param int    $propositionId  ID de la proposition acceptée
     * @param string $postTitle      Titre du post (pour le message)
     */
    public function __construct(
        public int $postId,
        public int $propositionId,
        public string $postTitle
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        // URL par défaut
        $url = '#';

        // ✅ Cible front : page du post + flag accepted=1
        if (Route::has('front.waste-posts.show')) {
            $url = route('front.waste-posts.show', [
                'postDechet' => $this->postId,
                'accepted'   => 1,
            ]);
        }
        // 🔁 Fallback utile : liste "Mes propositions" avec surbrillance
        elseif (Route::has('front.propositions.index')) {
            $url = route('front.propositions.index', [
                'highlight' => $this->propositionId,
            ]);
        }
        // 🔁 Dernier fallback (backoffice)
        elseif (Route::has('propositions.show')) {
            $url = route('propositions.show', $this->propositionId);
        }

        return [
            'type'           => 'proposal_accepted',
            'message'        => "Votre proposition pour « {$this->postTitle} » a été acceptée.",
            'proposition_id' => $this->propositionId,
            'post_id'        => $this->postId,
            'url'            => $url,
        ];
    }
}
