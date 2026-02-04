<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Verificar datos de enero 2026
$eneroQuery = \App\Models\EquipoComputo::whereHas('cabecera', function ($q) {
    $q->whereMonth('fecha', 1)->whereYear('fecha', 2026);
});

$totalEnero = $eneroQuery->count();

echo "Equipos en Enero 2026: $totalEnero\n";

// Verificar datos de febrero 2026
$febreroQuery = \App\Models\EquipoComputo::whereHas('cabecera', function ($q) {
    $q->whereMonth('fecha', 2)->whereYear('fecha', 2026);
});

$totalFebrero = $febreroQuery->count();

echo "Equipos en Febrero 2026: $totalFebrero\n";

// Total general
echo "Total general: " . \App\Models\EquipoComputo::count() . "\n";
