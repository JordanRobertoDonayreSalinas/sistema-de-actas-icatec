<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE DATOS DE EQUIPOS ===\n\n";

// Total de equipos
$total = \App\Models\EquipoComputo::count();
echo "Total de equipos en BD: $total\n\n";

// Equipos con cabecera
$conCabecera = \App\Models\EquipoComputo::whereHas('cabecera')->count();
echo "Equipos con cabecera: $conCabecera\n\n";

// Obtener equipos con sus fechas
$equipos = \App\Models\EquipoComputo::with('cabecera')->get();
$porMes = [];

foreach ($equipos as $equipo) {
    if ($equipo->cabecera && $equipo->cabecera->fecha) {
        $periodo = $equipo->cabecera->fecha->format('Y-m');
        if (!isset($porMes[$periodo])) {
            $porMes[$periodo] = 0;
        }
        $porMes[$periodo]++;
    }
}

krsort($porMes);

echo "Equipos por mes:\n";
foreach (array_slice($porMes, 0, 12, true) as $periodo => $cantidad) {
    echo "  $periodo: $cantidad equipos\n";
}

echo "\n";

