<?php

namespace App\Mail;

use App\Models\OffreTroc;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NouvelleOffreTrocMail extends Mailable
{
    use Queueable, SerializesModels;

    public $offre;
    public $postOwner;

    public function __construct(OffreTroc $offre)
    {
        $this->offre = $offre;
        $this->postOwner = $offre->postDechet->user;
    }

    public function build()
    {
        return $this->subject('Nouvelle offre de troc sur votre post')
                    ->view('emails.nouvelle_offre_troc');
    }
}
