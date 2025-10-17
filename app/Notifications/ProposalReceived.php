<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class ProposalReceived extends Notification
{
    use Queueable;

    public function __construct(
        public int $postId,
        public int $propositionId,
        public string $senderName,
        public string $postTitle
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

   public function toDatabase($notifiable): array
{
    return [
        'type'           => 'proposal_received',
        'message'        => "{$this->senderName} a envoyé une proposition pour « {$this->postTitle} ».",
        'post_id'        => $this->postId,
        'proposition_id' => $this->propositionId,
        // Ouvre la page du post avec surbrillance de la proposition
        'url'            => route('front.waste-posts.show', [
            'postDechet' => $this->postId,
            'highlight'  => $this->propositionId,
        ]) . '#propositions',
    ];
}

}
