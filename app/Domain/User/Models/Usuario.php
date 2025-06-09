<?php

namespace App\Domain\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    //protected $table = 'users';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'email', 'contraseña', 'rol', 'dni', 'telefono', 'estado', 'fecha_nacimiento', 'fecha_alta'
    ];

    protected $hidden = [
        'contraseña',
    ];
}
