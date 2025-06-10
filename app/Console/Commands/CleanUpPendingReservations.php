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
    protected $description = 'Elimina reservas con estado "pendiente" que no han sido pagadas en un tiempo determinado.';

    public function handle()
    {
        $minutesThreshold = 1; // Define el umbral de tiempo en minutos

        // Carga las relaciones 'cliente' y 'maquinaria' para poder acceder a sus datos en el Mailable
        $reservasAEliminar = Reserva::where('estado', 'pendiente')
            ->where('fecha_reserva', '<=', Carbon::now()->subMinutes($minutesThreshold))
            ->with(['cliente', 'maquinaria']) // Carga las relaciones
            ->get();

        if ($reservasAEliminar->isEmpty()) {
            $this->info('No hay reservas pendientes de pago para eliminar.');
            return Command::SUCCESS;
        }

        foreach ($reservasAEliminar as $reserva) {
            // Verifica que el cliente y su email existan antes de intentar enviar el correo
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
            
            $reserva->delete(); // Elimina la reserva
            $this->info("Reserva ID {$reserva->id_reserva} (estado pendiente) eliminada por falta de pago.");
        }

        $this->info('Proceso de limpieza de reservas pendientes de pago completado.');

        return Command::SUCCESS;
    }
}