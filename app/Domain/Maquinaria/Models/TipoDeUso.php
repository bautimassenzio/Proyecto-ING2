<?php

namespace App\Domain\Maquinaria\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDeUso extends Model
{
    use HasFactory;

    protected $table = 'tipos_de_uso';

    protected $fillable = ['nombre'];

    public function maquinarias()
    {
        return $this->hasMany(Maquinaria::class, 'tipo_de_uso_id');
    }
}