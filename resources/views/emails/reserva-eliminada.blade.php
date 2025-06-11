@component('mail::message')
# Reserva Eliminada

Hola {{ $reserva->cliente->nombre ?? 'Estimado cliente' }},

Lamentamos informarte que tu reserva **#{{ $reserva->id_reserva }}** para la maquinaria **{{ $reserva->maquinaria->marca}}{{ $reserva->maquinaria->modelo }}** ha sido eliminada.

Esto se debe a que el pago no fue confirmado dentro del plazo establecido de **24 horas**.

**Detalles de la reserva eliminada:**
* **Maquinaria:** {{ $reserva->maquinaria->nombre ?? 'N/A' }}
* **Fechas:** del {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}
* **Total:** ${{ number_format($reserva->total, 2, ',', '.') }}

Si todavía deseas reservar esta maquinaria u otra, por favor, realiza una nueva reserva.

@component('mail::button', ['url' => route('reservas.create')])
Realizar una nueva reserva
@endcomponent

Gracias por tu comprensión.

Saludos,
El equipo de {{ config('app.name') }}
@endcomponent