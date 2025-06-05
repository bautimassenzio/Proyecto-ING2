@component('mail::message')
# Cancelación de Reserva

Hola {{ $reserva->cliente->name }},

Tu reserva para la maquinaria **{{ $reserva->maquinaria->marca }} {{ $reserva->maquinaria->modelo }}** 
con fecha de inicio {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }} ha sido cancelada exitosamente.

**Política de cancelación aplicada:**  
@switch($politicaCancelacion)
    @case('reembolso_parcial')
        Reembolso parcial
        Se aplicará una penalización del 20% sobre el total de la reserva.  
        Total reservado: ${{ number_format($reserva->total, 2) }}  
        Penalización: ${{ number_format($reserva->total * 0.20, 2) }}  
        Monto a reembolsar: ${{ number_format($reserva->total * 0.80, 2) }}
        @break
    @case('sin_reembolso')
        Sin reembolso
        Se aplicará la penalización del 100%, no hay reembolso.
        @break
    @case('reembolso_total')
        Reembolso total
        No se aplican penalizaciones, tienes reembolso completo.
        @break
    @default
        Política no definida. Contacta soporte para más información.
@endswitch

Gracias por usar nuestro servicio.

Saludos,  
MannyMaquinarias
@endcomponent
