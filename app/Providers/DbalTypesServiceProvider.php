<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\DBAL\Types\Type; // Importa la clase Type de Doctrine DBAL
use Doctrine\DBAL\Platforms\MySqlPlatform; // Importa la plataforma MySQL si usas MySQL
use Illuminate\Support\Facades\Schema;

class DbalTypesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra el tipo 'enum' si aún no está registrado
        // Esto es crucial para que Doctrine DBAL reconozca el tipo ENUM.
        if (!Type::hasType('enum')) {
            Type::addType('enum', 'Doctrine\DBAL\Types\StringType');
        }

        // Si estás usando MySQL, asegúrate de que la plataforma mapee 'enum' correctamente.
        // Esto es útil si Doctrine intenta introspectar la base de datos y no sabe qué hacer con ENUM.
        // Solo si usas MySQL:
        $platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
        if ($platform instanceof MySqlPlatform) {
            $platform->registerDoctrineTypeMapping('enum', 'enum');
        }
    }
}
