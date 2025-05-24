<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Usaremos DB facade para insertar directamente

class MaquinariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta un registro de maquinaria.
        // id_maquinaria se auto-incrementará si no lo especificas,
        // si quieres forzar que el primer registro sea ID 1, puedes incluirlo.
        DB::table('maquinarias')->insert([
            // 'id_maquinaria' => 1, // Descomenta si quieres forzar el ID 1 para la primera inserción
            'nro_inventario' => 'M001-EXCA',
            'marca' => 'Caterpillar',
            'modelo' => '320D',
            'anio' => 2023,
            'tipo_energia' => 'combustion',
            'uso' => 'Excavación de terrenos',
            'localidad' => 'San Carlos',
            'precio_dia' => 450.50,
            'foto_url' => null,
            'estado' => 'disponible',
            'id_politica' => null,
        ]);

        // Ejemplo de otra maquinaria (se auto-incrementará el ID)
        DB::table('maquinarias')->insert([
            'nro_inventario' => 'M002-RETRO',
            'marca' => 'Komatsu',
            'modelo' => 'PC210',
            'anio' => 2022,
            'tipo_energia' => 'electrica', // Ejemplo diferente
            'uso' => 'Demolición y carga',
            'localidad' => 'La Plata',
            'precio_dia' => 380.00,
            'foto_url' => null,
            'estado' => 'disponible',
            'id_politica' => null,
        ]);
    }
}
