<?php

namespace App\Domain\Maquinaria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquinaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nro_inventario',
        'marca',
        'modelo',
        'precio_dia',
        'foto_url',
    ];
}