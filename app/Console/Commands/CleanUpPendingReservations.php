<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Reserva\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail; // Importa la fachada Mail
use App\Mail\ReservaEliminada;

class CleanUpPendingReservations extends Command
{
    protected $signature = 'reservas:cleanup-pending';
    protected $description = 'Cancelar reservas con estado "pendiente" que no han sido pagadas en un tiempo determinado.';

    public function handle()
{
    $minutesThreshold = 1440; // Umbral de tiempo en minutos

    $reservasAEliminar = Reserva::where('estado', 'pendiente')
        ->where('created_at', '<=', Carbon::now()->subMinutes($minutesThreshold))
        ->with(['cliente', 'maquinaria'])
        ->get();

    if ($reservasAEliminar->isEmpty()) {
        $this->info('No hay reservas pendientes de pago para cancelar.');
        return Command::SUCCESS;
    }

    foreach ($reservasAEliminar as $reserva) {
        if ($reserva->cliente && $reserva->cliente->email) {
            try {
                Mail::to($reserva->cliente->email)->send(new ReservaEliminada($reserva));
                $this->info("Correo de cancelación enviado a {$reserva->cliente->email} para Reserva ID: {$reserva->id_reserva}.");
            } catch (\Exception $e) {
                $this->error("Error al enviar correo de cancelación para Reserva ID: {$reserva->id_reserva}: " . $e->getMessage());
            }
        } else {
            $this->warn("No se pudo enviar correo de cancelación para Reserva ID: {$reserva->id_reserva}. Cliente o email no encontrado.");
        }

        // CAMBIO: ya no se elimina, solo se cancela
        $reserva->estado = 'cancelada';
        $reserva->save();
        $this->info("Reserva ID {$reserva->id_reserva} marcada como cancelada por falta de pago.");
    }

    $this->info('Proceso de cancelación de reservas pendientes de pago completado.');
    return Command::SUCCESS;
}

}