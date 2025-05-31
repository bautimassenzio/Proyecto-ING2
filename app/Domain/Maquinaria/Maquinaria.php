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
    ];

    public function politica()
    {
        return $this->belongsTo(Politica::class, 'id_politica', 'id_politica');
        // El primer 'id_politica' es la FK en Maquinaria,
        // el segundo 'id_politica' es la PK en Politica
    }
}