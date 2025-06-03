<?php

namespace App\Domain\Pago\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_reserva',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'estado_pago',
    ];

    public $timestamps = false;
}