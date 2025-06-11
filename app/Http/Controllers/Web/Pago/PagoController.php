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
use App\Domain\Pago\Models\ValidCard;

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
        $item->title = 'Reserva';
        $item->quantity = 1;
        $item->unit_price = (float)$reserva->total; // Asegúrate de que sea un float

        $preference = new \MercadoPago\Preference();
        $preference->items = [$item];

   $successUrl = route('pago.exito');
$failureUrl = route('pago.fallo');
$pendingUrl = route('pago.pendiente');

$ngrokBase = 'https://3984-2800-340-52-144-50b3-7086-7165-1501.ngrok-free.app'; // tu URL actual de ngrok

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

    public function mostrarFormularioTarjeta()
    {   
        // Recuperar el id_reserva de la sesión
        $reserva_id = session('reserva_id');

        // Opcional: Si el id_reserva no está en la sesión, puedes redirigir o mostrar un error
        if (empty($reserva_id)) {
            Log::warning('Pago Tarjeta: Intento de acceder al formulario sin reserva_id en sesión.');
            return redirect()->route('reservas.create')->withErrors(['error' => 'No hay una reserva activa para procesar.']);
        }

        // Pasar el id_reserva a la vista del formulario de la tarjeta
        return view('pago.formulario_tarjeta', compact('reserva_id'));
    }

    public function procesarPagoTarjeta(Request $request)
    {
        // 1. Validar los datos del formulario
       $request->validate([
            'card_number' => 'required|string',
            'expiry_month' => 'required|digits:2|min:1|max:12',
            'expiry_year' => 'required|digits:2',
            'cvv' => 'required|digits_between:3,4',
            'reserva_id' => 'required|exists:reservas,id_reserva',
        ], [
            'card_number.required' => 'El número de tarjeta es obligatorio.',
            'card_number.string' => 'El número de tarjeta debe ser una cadena de texto.',
            
            'expiry_month.required' => 'El mes de expiración es obligatorio.',
            'expiry_month.digits' => 'El mes de expiración debe tener exactamente 2 dígitos.',
            'expiry_month.min' => 'El mes de expiración no puede ser menor que 1.',
            'expiry_month.max' => 'El mes de expiración no puede ser mayor que 12.',
            
            'expiry_year.required' => 'El año de expiración es obligatorio.',
            'expiry_year.digits' => 'El año de expiración debe tener exactamente 2 dígitos.',
            
            'cvv.required' => 'El código CVV es obligatorio.',
            'cvv.digits_between' => 'El código CVV debe tener entre 3 y 4 dígitos.',
            
            'reserva_id.required' => 'El campo de reserva es obligatorio.',
            'reserva_id.exists' => 'La reserva seleccionada no existe.',
        ]);


        // 2. Obtener el ID de la reserva enviado desde el campo oculto del formulario
        $idreserva = $request->input('reserva_id');

        // 3. Cargar la reserva desde la base de datos
        // Importante: Asegurarse de que la relación 'maquinaria' esté cargada si se necesita en el Mailable
        $reserva = Reserva::with('maquinaria')->find($idreserva); // Carga la reserva y su maquinaria

        // Verificar si la reserva fue encontrada
        if (!$reserva) {
            Log::error('Pago Tarjeta: Reserva no encontrada para ID: ' . $idreserva);
            $mensaje = 'Error: No se encontró la reserva asociada al pago.';
            return view('pago.botonhome', compact('mensaje')); // O redirige a una ruta de error
        }

        // 4. Buscar una tarjeta que coincida en la base de datos (simulación)
        $tarjetaValida = ValidCard::where('card_number', $request->input('card_number'))
            ->where('expiry_month', $request->input('expiry_month'))
            ->where('expiry_year', $request->input('expiry_year'))
            ->where('cvv', $request->input('cvv'))
            ->first();

        // 5. Procesar el pago si la tarjeta es válida
        if ($tarjetaValida) {
            // --- NUEVA LÓGICA: Verificar conexión y saldo ---

            // Si la conexión no es válida
            if (!$tarjetaValida->conexion) {
                Log::warning('Intento de pago con tarjeta sin conexión válida: ' . $request->input('card_number'));
                return redirect()->route('pago.procesar.tarjeta')->withErrors(['tarjeta' => 'No pudimos establecer conexión con la tarjeta. Intenta de nuevo.'])->withInput($request->only('card_number', 'expiry_month', 'expiry_year'));
            }

            // Si hay conexión pero el saldo es insuficiente
            if (!$tarjetaValida->saldo) {
                Log::warning('Intento de pago con tarjeta con saldo insuficiente: ' . $request->input('card_number'));
                return redirect()->route('pago.procesar.tarjeta')->withErrors(['tarjeta' => 'Saldo insuficiente en la tarjeta. Por favor, verifica tus fondos o usa otra tarjeta.'])->withInput($request->only('card_number', 'expiry_month', 'expiry_year'));
            }

            // --- FIN NUEVA LÓGICA ---

            // Simulación de pago exitoso (si la tarjeta es válida, tiene conexión y saldo)
            $monto = $reserva->total;
            $estado_pago = 'completo';
            $metodo = 'tarjeta'; // Indicamos que el método de pago es tarjeta
            $fecha_pago = now();

            try {
                // Crear el registro de pago en tu tabla 'pagos'
                $pago = Pago::create([
                    'id_reserva' => $idreserva,
                    'monto' => $monto,
                    'fecha_pago' => $fecha_pago,
                    'metodo_pago' => $metodo,
                    'estado_pago' => $estado_pago,
                ]);

                // Actualizar el estado de la reserva
                $reserva->estado = 'aprobada'; // O el estado final que corresponda
                $reserva->save();

                // 6. Enviar el correo de confirmación de reserva (si aplica para pagos con tarjeta)
                $cliente = Usuario::find($reserva->id_cliente);
                if ($cliente && $cliente->email) {
                    // El objeto $reserva ya tiene la relación 'maquinaria' cargada.
                    Mail::to($cliente->email)->send(new ConfirmacionReserva($reserva, $cliente));
                    Log::info('Correo de confirmación de reserva enviado para Reserva ID: ' . $reserva->id_reserva . ' y Cliente: ' . $cliente->email . ' (Pago con Tarjeta)');
                } else {
                    Log::warning('No se pudo enviar correo de confirmación: Cliente o email no encontrados para Reserva ID: ' . $reserva->id_reserva . ' (Pago con Tarjeta)');
                }

                // 7. Redirigir a una vista de éxito
                $mensaje = '✅ Pago exitoso con tarjeta. Confirmamos tu reserva.';
                return view('pago.botonhome', compact('mensaje'));

            } catch (\Exception $e) {
                // Capturar y loguear cualquier error durante la creación del pago o el envío del correo
                Log::error('Error en PagoController@procesarPagoTarjeta: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'reserva_id' => $idreserva]);
                $mensaje = '❌ Error interno al procesar la confirmación del pago con tarjeta.';
                return view('pago.botonhome', compact('mensaje'));
            }
        } else {
            // Si la tarjeta no se encontró por datos incorrectos
            return redirect()->route('pago.procesar.tarjeta')->withErrors(['tarjeta' => 'Los datos de la tarjeta son incorrectos o la tarjeta no es válida.'])->withInput($request->only('card_number', 'expiry_month', 'expiry_year'));
        }
    }
    


}