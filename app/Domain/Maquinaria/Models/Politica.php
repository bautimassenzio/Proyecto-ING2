<?php

namespace App\Domain\Maquinaria\Models; // O tu namespace, ej. App\Domain\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Politica extends Model
{
    use HasFactory;

    // Define el nombre de la tabla si no sigue la convención de pluralización (ej. 'politicas' por defecto)
    // protected $table = 'politicas'; // No es estrictamente necesario si la tabla se llama 'politicas'

    // Define la clave primaria, ya que no es 'id'
    protected $primaryKey = 'id_politica';

    // Indica que la clave primaria es auto-incrementable
    public $incrementing = true;

    // Define el tipo de la clave primaria (opcional, 'int' es el predeterminado para integer)
    protected $keyType = 'int';

    // Define las columnas que pueden ser asignadas masivamente (fillable)
    protected $fillable = [
        'tipo',
        'detalle',
    ];

    // Opcional: Si no usas timestamps (created_at, updated_at) en la tabla 'politicas'
    // public $timestamps = false;
}