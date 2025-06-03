<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Domain\Reserva\Models\Reserva;

class ReservaCancelada extends Mailable
{
    use Queueable, SerializesModels;

    public $reserva;
    public $politicaCancelacion;

    public function __construct(Reserva $reserva, $politicaCancelacion)
    {
        $this->reserva = $reserva;
        $this->politicaCancelacion = $politicaCancelacion;
    }

    public function build()
    {
        return $this->subject('Cancelación de tu reserva')
                    ->markdown('emails.reserva_cancelada');
    }
}
