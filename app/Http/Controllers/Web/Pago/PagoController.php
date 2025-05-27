<?php

namespace App\Http\Controllers\Web\Pago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MercadoPago\SDK;
use App\Domain\Pago\Models\Pago;

class PagoController extends Controller
{
    public function pagar()
    {
        SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

        $preference = new \MercadoPago\Preference();

        $idreserva = 1; // ← Este debería ser dinámico
        // Crear un ítem de ejemplo
        $item = new \MercadoPago\Item();
        $item->title = 'Reserva de habitación';
        $item->quantity = 1;
        $item->unit_price = 5000; // Precio en ARS

        $preference->items = [$item];

        //$preference->auto_return = "approved"; //Por ahora esto da error en la API de MP

        // URLs de retorno
        $preference->back_urls = [
            "success" => route('pago.exito'),
            "failure" => route('pago.fallo'),
            "pending" => route('pago.pendiente')
        ];
        

        $preference->external_reference = $idreserva;
        $preference->save();
        //dd($preference);

        $publicKey = env('MERCADOPAGO_PUBLIC_KEY');

        return redirect()->away($preference->sandbox_init_point);
    }

   public function exito(Request $request) {
    // Simulamos los datos recibidos tras el pago (hay que adaptar)
    $idreserva = $request->query('external_reference'); // id de la reserva pasada al crear la preferencia
    $monto = $request->query('monto'); 
    $estado = 'completo';
    $metodo = 'mercadopago';
    $fecha_pago = now();

    // Registrar en la base de datos
    Pago::create([
        'id_reserva' => $idreserva,
        'monto' => $monto,
        'fecha_pago' => $fecha_pago,
        'metodo_pago' => $metodo,
        'estado_pago' => $estado,
    ]);


    $mensaje = '✅ Pago exitoso. Confirmamos tu reserva.';
    return view('pago.botonhome', compact('mensaje'));
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