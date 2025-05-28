<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\User\Models\Usuario;

class EnviarContraseña extends Mailable {

    public $nombre;
    public $contraseñaGenerada;

    public function __construct(string $nombre, string $contraseñaGenerada)
    {
        $this->nombre = $nombre;
        $this->contraseñaGenerada = $contraseñaGenerada;
    }
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido - Tu contraseña de acceso',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.envio_Contraseña', // <-- Aquí es donde se carga la vista HTML
            with: [
                'nombreCliente' => $this->nombre,
                'contraseña' => $this->contraseñaGenerada,
            ],
        );
    }


}