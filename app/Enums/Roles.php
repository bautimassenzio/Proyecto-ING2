<?php

namespace App\Enums;

enum Roles: string
{
    case EMPLEADO = 'empleado';
    case CLIENTE = 'cliente';
    case ADMINISTRADOR = 'administrador';
}
