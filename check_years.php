<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Verificar datos por año
for ($year = 2024; $year <= 2026; $year++) {
    $total = \App\Models\EquipoComputo::whereHas('cabecera', function ($q) use ($year) {
        $q->whereYear('fecha', $year);
    })->count();

    echo "Equipos en $year: $total\n";

    if ($total > 0) {
        // Mostrar distribución por mes para este año
        for ($month = 1; $month <= 12; $month++) {
            $totalMes = \App\Models\EquipoComputo::whereHas('cabecera', function ($q) use ($year, $month) {
                $q->whereYear('fecha', $year)->whereMonth('fecha', $month);
            })->count();

            if ($totalMes > 0) {
                $meses = [
                    '',
                    'Enero',
                    'Febrero',
                    'Marzo',
                    'Abril',
                    'Mayo',
                    'Junio',
                    'Julio',
                    'Agosto',
                    'Septiembre',
                    'Octubre',
                    'Noviembre',
                    'Diciembre'
                ];
                echo "  - {$meses[$month]} $year: $totalMes equipos\n";
            }
        }
    }
}
