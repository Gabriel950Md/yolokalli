<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DonacionMail extends Mailable
{
    use SerializesModels;

    public $info;

    public function __construct($data)
    {
        $this->info = $data;
    }

    public function build()
    {
        return $this->subject('Nueva Donación Recibida')
            ->view('emails.donacion');
    }
}
