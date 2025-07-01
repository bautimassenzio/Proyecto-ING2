<?php

namespace App\Domain\Maquinaria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maquinaria extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id_maquinaria';
    public $incrementing= true;
    protected $fillable = [
        'nro_inventario',
        'marca',
        'modelo',
        'precio_dia',
        'foto_url',
        'anio',
        'uso',
        'tipo_energia',
        'estado',
        'localidad',
        'id_politica',
        'descripcion',
    ];

    public function politica()
    {
        return $this->belongsTo(Politica::class, 'id_politica', 'id_politica');
        // El primer 'id_politica' es la FK en Maquinaria,
        // el segundo 'id_politica' es la PK en Politica
    }

     public function reservas()
    {
        // 'Reserva::class' es el modelo de Reserva
        // 'id_maquinaria' es la FK en la tabla 'reservas'
        // 'id_maquinaria' es la PK en la tabla 'maquinarias'
        return $this->hasMany(Reserva::class, 'id_maquinaria', 'id_maquinaria');
    }

    protected static function boot()
    {
        
        parent::boot();

        // --- INICIO: REGISTRO DE DEPURACIÓN EN EL MÉTODO BOOT ---
        Log::info('Maquinaria::boot() method fired. Registering deleting event.');
        // --- FIN: REGISTRO DE DEPURACIÓN ---

        // Escucha el evento 'deleting'
        static::deleting(function ($maquinaria) {
            // --- INICIO: REGISTRO DE DEPURACIÓN DENTRO DEL EVENTO DELETING ---
            Log::info('Maquinaria deleting event triggered for ID: ' . $maquinaria->id_maquinaria);

            $reservasActivasExist = $maquinaria->reservas()
                                                ->whereIn('estado_reserva', ['activa', 'pendiente'])
                                                ->exists();

            Log::info('Maquinaria ID ' . $maquinaria->id_maquinaria . ' - Reservas activas encontradas: ' . ($reservasActivasExist ? 'true' : 'false'));
            // --- FIN: REGISTRO DE DEPURACIÓN ---

            if ($reservasActivasExist) {
                Log::warning('Maquinaria ID ' . $maquinaria->id_maquinaria . ': Bloqueando baja por reservas activas.');
                return false; // Esto es CRUCIAL. Detiene la operación de borrado.
            }

            Log::info('Maquinaria ID ' . $maquinaria->id_maquinaria . ': No hay reservas activas. Procediendo con el cambio de estado a inactiva.');
            $maquinaria->estado = 'inactiva';
            $maquinaria->save();
        });
    }
}