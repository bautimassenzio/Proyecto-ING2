<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MaquinariaSeeder::class, // ¡Asegúrate de que esta línea esté aquí!
            // Otros seeders si tienes
        ]);
    }
}