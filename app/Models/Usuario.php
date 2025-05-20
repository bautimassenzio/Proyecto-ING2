<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    public $timestamps = false;

    // Campos que se pueden cargar masivamente (ej: create o update)
    protected $fillable = [
        'nombre',
        'email',
        'contraseña',
        'rol',
        'dni',
        'telefono',
        'estado',
        'fecha_alta',
    ];

    
}
