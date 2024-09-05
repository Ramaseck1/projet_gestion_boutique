<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;

class ClientCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $card;

    public function __construct(Client $client, $card)
    {
        $this->client = $client;
        $this->card = $card;
    }

    public function build()
    {
        return $this->view('welcome.blade.php')
                    ->attachData($this->card, 'client_card.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->subject('Votre carte de client avec QR Code');
    }
}
