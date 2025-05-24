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

class ConfirmacionReserva extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;
    public $cliente;

    /**
     * Create a new message instance.
     */
    public function __construct(Reserva $reserva, Usuario $cliente)
    {
        $this->reserva = $reserva;
        $this->cliente = $cliente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de tu Reserva - ¡Gracias!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.confirmacion_reserva', // <-- Aquí es donde se carga la vista HTML
            with: [
                'nombreCliente' => $this->cliente->nombre,
                'fechaInicio' => $this->reserva->fecha_inicio,
                'fechaFin' => $this->reserva->fecha_fin,
                'pagoTotal' => $this->reserva->total, // Usar $this->reserva->total
                'idReserva' => $this->reserva->id_reserva,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}