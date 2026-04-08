<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$backupFile = 'backup_pro.sql';
if (!file_exists($backupFile)) {
    die("Error: No se encuentra backup_pro.sql\n");
}

echo "Iniciando fusión de datos de producción...\n";

// Deshabilitar checks de llaves foráneas para permitir inserción en cualquier orden
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$handle = fopen($backupFile, "r");
$currentQuery = "";
$inInsert = false;
$successCount = 0;
$failCount = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $trimmed = trim($line);
        
        // Empezar a capturar si es un INSERT
        if (stripos($trimmed, 'INSERT INTO') === 0) {
            $inInsert = true;
            $currentQuery = $line;
        } elseif ($inInsert) {
            $currentQuery .= $line;
        }

        // Si la línea termina en ; y estamos en un INSERT, ejecutamos
        if ($inInsert && str_ends_with($trimmed, ';')) {
            try {
                DB::unprepared($currentQuery);
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                // Error silencioso para no llenar el log, solo informar progreso
                if ($failCount % 50 == 0) echo "Procesados con errores: $failCount...\n";
            }
            $currentQuery = "";
            $inInsert = false;
        }
    }
    fclose($handle);
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Fusión completada.\n";
echo "Bloques INSERT exitosos: $successCount\n";
echo "Bloques INSERT fallidos: $failCount\n";
