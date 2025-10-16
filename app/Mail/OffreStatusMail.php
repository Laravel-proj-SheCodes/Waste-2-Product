<?php
// app/Mail/OffreStatusMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OffreStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $offre;
    public $post;
    public $status; // 'accepted' ou 'rejected'

    public function __construct($offre, $post, $status)
    {
        $this->offre = $offre;
        $this->post = $post;
        $this->status = $status;
    }

    public function build()
    {
        $subject = $this->status === 'accepted' ? 'Votre offre a été acceptée' : 'Votre offre a été refusée';

        return $this->subject($subject)
                    ->view('emails.offre-status'); // Blade à créer
    }
}
