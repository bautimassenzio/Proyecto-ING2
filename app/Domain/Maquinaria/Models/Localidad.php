<?php

namespace App\Domain\Maquinaria\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'localidades';

    protected $fillable = ['nombre'];

    public function maquinarias()
    {
        return $this->hasMany(Maquinaria::class, 'localidad_id');
    }
}