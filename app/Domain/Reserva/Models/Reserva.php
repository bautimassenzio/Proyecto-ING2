<?php

namespace App\Domain\Reserva\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\Usuario; // Importa el modelo Usuario
use App\Domain\Maquinaria\Models\Maquinaria;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';
    public $timestamps = true; // Mantén true para created_at y updated_at

    protected $fillable = [
        'id_cliente',
        'id_maquinaria', // Descomentar cuando implementes maquinaria
        'fecha_inicio',
        'fecha_fin',
        'fecha_reserva', // Añadido
        'estado',        // Cambiado de estado_reserva a estado
        'total',         // Cambiado de pago_total a total
        'id_empleado',   // Cambiado de id_empleado_entrega a id_empleado
    ];

    public function maquinaria()
{
    return $this->belongsTo(Maquinaria::class, 'id_maquinaria');
}


    /**
     * Relación con el usuario que realiza la reserva (el cliente).
     */
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'id_cliente', 'id_usuario');
    }

    /**
     * Relación con el empleado asignado a la reserva (se asigna después).
     */
    public function empleado()
    {
        return $this->belongsTo(Usuario::class, 'id_empleado', 'id_usuario');
    }
}