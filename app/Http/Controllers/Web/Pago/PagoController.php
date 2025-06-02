<?php

namespace App\Http\Controllers\Web\Pago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use App\Domain\Pago\Models\Pago;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\User\Models\Usuario;
use App\Mail\ConfirmacionReserva;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // Agrega esta línea para usar el log de Laravel

class PagoController extends Controller
{
    public function pagar()
    {
        // 1. Verificar que el Access Token se está cargando correctamente
        $accessToken = config('mercadopago.token');
        if (empty($accessToken)) {
            Log::error('Mercado Pago: Access Token no configurado o vacío.');
            return redirect()->route('reservas.create')->withErrors(['error' => 'Error de configuración: Access Token de Mercado Pago no encontrado.']);
        }
        SDK::setAccessToken($accessToken);

        $idreserva = session('reserva_id');

        $reserva = Reserva::find($idreserva);
        if (!$reserva) {
            Log::error('Mercado Pago: Reserva no encontrada para ID: ' . $idreserva);
            return redirect()->route('reservas.create')->withErrors(['error' => 'Reserva no encontrada.']);
        }

        // 2. Verificar el valor del total de la reserva
        if (!isset($reserva->total) || !is_numeric($reserva->total) || $reserva->total <= 0) {
            Log::error('Mercado Pago: El total de la reserva es inválido o cero. Total: ' . $reserva->total . ' para Reserva ID: ' . $idreserva);
            return redirect()->route('reservas.create')->withErrors(['error' => 'El monto de la reserva es inválido para el pago.']);
        }

        $item = new \MercadoPago\Item();
        $item->title = 'Reserva de habitación';
        $item->quantity = 1;
        $item->unit_price = (float)$reserva->total; // Asegúrate de que sea un float

        $preference = new \MercadoPago\Preference();
        $preference->items = [$item];

   $successUrl = route('pago.exito');
$failureUrl = route('pago.fallo');
$pendingUrl = route('pago.pendiente');

$ngrokBase = 'https://365e-2802-8012-106-9600-b839-2ac3-2414-a8d5.ngrok-free.app'; // tu URL actual de ngrok

$preference->back_urls = [
    "success" => $ngrokBase . '/pago/exito',
    "failure" => $ngrokBase . '/pago/fallo',
    "pending" => $ngrokBase . '/pago/pendiente',
];



        $preference->external_reference = $idreserva;
        $preference->auto_return = 'approved';

        // 3. Imprimir el objeto Preference antes de intentar guardarlo
        Log::info('Mercado Pago: Objeto Preference antes de save:', (array) $preference);

        try {
            $preference->save(); // Intenta guardar la preferencia
        } catch (\Exception $e) {
            // 4. Capturar cualquier excepción que pueda ocurrir durante el save()
            Log::error('Mercado Pago: Excepción al guardar la preferencia: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('reservas.create')->withErrors(['error' => 'Error interno al procesar el pago.']);
        }

        // 5. Imprimir el objeto Preference después de intentar guardarlo
        Log::info('Mercado Pago: Objeto Preference después de save:', (array) $preference);

        // 6. Verificar si se generó la URL de sandbox o producción
        $redirectUrl = null;
        if (config('mercadopago.sandbox')) {
            $redirectUrl = $preference->sandbox_init_point;
        } else {
            $redirectUrl = $preference->init_point;
        }

        if (empty($redirectUrl)) {
            // Este es el dd que te está dando el error
            Log::error('Mercado Pago: No se generó URL de pago. Objeto Preference final:', (array) $preference);
            return redirect()->route('reservas.create')->withErrors(['error' => 'Error: No se pudo generar la URL de pago de Mercado Pago.']);
        }

        return redirect()->away($redirectUrl);
    }

    // ... (tus funciones exito, fallo, pendiente)
   public function exito(Request $request) {
        $idreserva = $request->query('external_reference');

        // *** CAMBIO CLAVE AQUÍ: Cargar la relación 'maquinaria' con la reserva ***
        // Usamos find() y luego load() para obtener la reserva y su maquinaria relacionada.
        $reserva = Reserva::find($idreserva); // Encuentra la reserva por su ID
        if ($reserva) {
            $reserva->load('maquinaria'); // Carga la maquinaria asociada a esta reserva
        }
        // **********************************************************************

        if (!$reserva) {
            $mensaje = 'Error: No se encontró la reserva asociada al pago.';
            Log::error('Pago Exito: Reserva no encontrada para external_reference: ' . $idreserva);
            return view('pago.botonhome', compact('mensaje'));
        }

        $monto = $reserva->total;
        $estado_pago = 'completo';
        $metodo = 'mercadopago';
        $fecha_pago = now();

        try {
            $pago = Pago::create([
                'id_reserva' => $idreserva,
                'monto' => $monto,
                'fecha_pago' => $fecha_pago,
                'metodo_pago' => $metodo,
                'estado_pago' => $estado_pago,
            ]);

            $reserva->estado = 'aprobada'; // O el estado final que corresponda
            $reserva->save();

            $cliente = Usuario::find($reserva->id_cliente);
            if ($cliente && $cliente->email) {
                // Ahora, $reserva tiene la relación 'maquinaria' cargada y disponible.
                Mail::to($cliente->email)->send(new ConfirmacionReserva($reserva, $cliente));
                Log::info('Correo de confirmación de reserva enviado para Reserva ID: ' . $reserva->id_reserva . ' y Cliente: ' . $cliente->email);
            } else {
                Log::warning('No se pudo enviar correo de confirmación: Cliente o email no encontrados para Reserva ID: ' . $reserva->id_reserva);
            }

            $mensaje = '✅ Pago exitoso. Confirmamos tu reserva.';
            return view('pago.botonhome', compact('mensaje'));

        } catch (\Exception $e) {
            Log::error('Error en PagoController@exito: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'reserva_id' => $idreserva]);
            $mensaje = '❌ Error interno al procesar la confirmación del pago.';
            return view('pago.botonhome', compact('mensaje'));
        }
    }


    public function fallo() {
        $mensaje = '❌ Ocurrió un error durante el pago.';
        return view('pago.botonhome', compact('mensaje'));
    }

    public function pendiente() {
        $mensaje = '⏳ Tu pago está pendiente.';
        return view('pago.botonhome', compact('mensaje'));
    }
}