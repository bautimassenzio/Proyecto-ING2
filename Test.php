<?php

// tinker_test_reservas.php

use App\Domain\Reserva\Models\Reserva; // Asegúrate de que este sea el namespace correcto
use Carbon\Carbon;

// Asegúrate de que los IDs de maquinaria y usuario sean reales y existan en tu BD
$idMaquinaria = 8; // Reemplaza con un ID de maquinaria real
$idUsuario = 1;    // Reemplaza con un ID de usuario real (el ID del cliente)

// --- Creación de Reservas de Prueba ---
// Reserva 1: Fecha de fin AYER (definitivamente lista para devolver)
$reserva1 = Reserva::create([
    'id_maquinaria' => $idMaquinaria,
    'id_cliente' => $idUsuario,
    'fecha_inicio' => Carbon::yesterday()->subDays(5),
    'fecha_fin' => Carbon::yesterday(),
    'fecha_reserva' => Carbon::now()->subDays(7),
    'estado' => 'aprobada',
    'total' => 500.00,
    'id_empleado' => null,
]);
dump("Reserva 1 creada con ID: " . $reserva1->id_reserva);

// Reserva 2: Fecha de fin HOY (también lista para devolver)
$reserva2 = Reserva::create([
    'id_maquinaria' => $idMaquinaria,
    'id_cliente' => $idUsuario,
    'fecha_inicio' => Carbon::today()->subDays(3),
    'fecha_fin' => Carbon::today(),
    'fecha_reserva' => Carbon::now()->subDays(5),
    'estado' => 'aprobada',
    'total' => 300.00,
    'id_empleado' => null,
]);
dump("Reserva 2 creada con ID: " . $reserva2->id_reserva);

// Opcional: Una reserva que NO debería aparecer (futura)
$reservaFutura = Reserva::create([
    'id_maquinaria' => $idMaquinaria,
    'id_cliente' => $idUsuario,
    'fecha_inicio' => Carbon::tomorrow(),
    'fecha_fin' => Carbon::tomorrow()->addDays(5),
    'fecha_reserva' => Carbon::now(),
    'estado' => 'aprobada',
    'total' => 500.00,
    'id_empleado' => null,
]);
dump("Reserva Futura creada con ID: " . $reservaFutura->id_reserva);

// --- Consulta para verificar las reservas "listas para devolver" ---
$reservasListas = Reserva::with(['maquinaria', 'cliente'])
    ->whereIn('estado', ['aprobada'])
    ->whereDate('fecha_fin', '<=', Carbon::today())
    ->orderBy('fecha_fin', 'asc')
    ->get();

dump("Resultados de la consulta 'Reservas Listas para Devolver':");
dump($reservasListas->toArray());

// Reserva 3: Aprobada, fecha de inicio FUTURA (debería aparecer en "Reservas Aprobadas para Entregar")
$reservaFuturaAprobada = Reserva::create([
    'id_maquinaria' => 9,
    'id_cliente' => $idUsuario,
    'fecha_inicio' => Carbon::tomorrow()->addDays(2), // Fecha de inicio en el futuro
    'fecha_fin' => Carbon::tomorrow()->addDays(7),
    'fecha_reserva' => Carbon::now(),
    'estado' => 'aprobada',
    'total' => 700.00,
    'id_empleado' => null,
]);
dump("Reserva 3 (Aprobada Futura) creada con ID: " . $reservaFuturaAprobada->id_reserva);

// Reserva 4: Aprobada, fecha de inicio HOY (debería aparecer en "Reservas Aprobadas para Entregar")
$reservaHoyAprobada = Reserva::create([
    'id_maquinaria' => 10,
    'id_cliente' => $idUsuario,
    'fecha_inicio' => Carbon::today(), // Fecha de inicio hoy
    'fecha_fin' => Carbon::today()->addDays(3),
    'fecha_reserva' => Carbon::now()->subDays(1),
    'estado' => 'aprobada',
    'total' => 450.00,
    'id_empleado' => null,
]);
dump("Reserva 4 (Aprobada Hoy) creada con ID: " . $reservaHoyAprobada->id_reserva);


// --- Consulta para verificar las reservas "listas para entregar" (todas las aprobadas no finalizadas/canceladas) ---
$reservasListas = Reserva::with(['maquinaria', 'cliente']) // <-- ¡CAMBIADO DE 'usuario' A 'cliente'!
    ->where('estado', 'aprobada')
    ->whereNotIn('estado', ['finalizada', 'cancelada'])
    ->orderBy('fecha_inicio', 'asc')
    ->get();

dump("Resultados de la consulta 'Reservas Aprobadas para Entregar':");
dump($reservasListas->toArray());