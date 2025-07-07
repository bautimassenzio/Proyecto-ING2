<?php

namespace App\Domain\Maquinaria\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Reserva\Models\Reserva;
use App\Domain\Maquinaria\Models\Localidad;
use App\Domain\Maquinaria\Models\TipoDeUso;
use App\Domain\Maquinaria\Models\Politica;


class Maquinaria extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_maquinaria';
    public $incrementing= true;
    public $timestamps = false;
    protected $fillable = [
        'nro_inventario',
        'marca',
        'modelo',
        'precio_dia',
        'foto_url',
        'anio',
        'tipo_de_uso_id',
        'tipo_energia',
        'estado',
        'localidad_id',
        'id_politica',
        'descripcion',
    ];

    public function politica()
    {
        return $this->belongsTo(Politica::class, 'id_politica', 'id_politica');
        // El primer 'id_politica' es la FK en Maquinaria,
        // el segundo 'id_politica' es la PK en Politica
    }

    public function reserva()
{
    return $this->hasMany(Reserva::class, 'id_maquinaria');
}

public function tipoDeUso()
{
    return $this->belongsTo(TipoDeUso::class, 'tipo_de_uso_id', 'id');
}

public function localidad()
{
    return $this->belongsTo(Localidad::class, 'localidad_id', 'id');
}


}