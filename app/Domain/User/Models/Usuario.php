<?php
//pingo
namespace App\Domain\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'email', 'contraseña', 'rol', 'dni', 'telefono', 'estado', 'fecha_alta'
    ];

    protected $hidden = [
        'contraseña',
    ];
}
