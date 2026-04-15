<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$prod_file = 'produccion.sql';

// Tablas a IGNORAR de producción (mantener data local)
$ignore_tables = [
    'incidencias', 'respuestas_incidencias', 'users', 'migrations',
    'sessions', 'cache', 'cache_locks', 'failed_jobs', 'jobs',
    'job_batches', 'password_reset_tokens'
];

echo "Leyendo produccion.sql linea por linea...\n";

$handle = fopen($prod_file, 'r');
if (!$handle) {
    die("Error: No se puede abrir produccion.sql\n");
}

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$current_statement = '';
$current_table = null;
$truncated_tables = [];
$insert_count = 0;
$skipped_tables = [];
$error_count = 0;

while (($line = fgets($handle)) !== false) {
    $line_trimmed = trim($line);

    // Detectar inicio de un INSERT INTO
    if (preg_match('/^INSERT INTO `([^`]+)`/i', $line_trimmed, $m)) {
        $current_table = $m[1];
    }

    // Si la tabla debe ignorarse, saltar todo
    if ($current_table !== null && in_array($current_table, $ignore_tables)) {
        // Si la línea termina el statement, reseteamos
        if (substr(rtrim($line), -1) === ';') {
            $current_table = null;
            $current_statement = '';
        }
        continue;
    }

    // Si hay una tabla activa que no existe localmente, saltar
    if ($current_table !== null && !Schema::hasTable($current_table)) {
        if (!isset($skipped_tables[$current_table])) {
            echo "Saltando (no existe local): $current_table\n";
            $skipped_tables[$current_table] = true;
        }
        if (substr(rtrim($line), -1) === ';') {
            $current_table = null;
            $current_statement = '';
        }
        continue;
    }

    // Acumular líneas del statement actual
    if ($current_table !== null) {
        $current_statement .= $line;

        // Si la línea termina con ;, el statement está completo
        if (substr(rtrim($line), -1) === ';') {
            // Vaciar la tabla si es la primera vez
            if (!isset($truncated_tables[$current_table])) {
                echo "Limpiando tabla: $current_table\n";
                try {
                    DB::table($current_table)->truncate();
                } catch (\Exception $e) {
                    echo "  [ERROR truncate] $current_table: " . $e->getMessage() . "\n";
                }
                $truncated_tables[$current_table] = true;
            }

            // Ejecutar el INSERT
            try {
                DB::unprepared($current_statement);
                $insert_count++;
                echo "  [OK] INSERT en $current_table\n";
            } catch (\Exception $e) {
                $error_count++;
                echo "  [ERROR insert] $current_table: " . $e->getMessage() . "\n";
            }

            // Resetear para el siguiente statement
            $current_statement = '';
            $current_table = null;
        }
    }
}

fclose($handle);
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n--- RESUMEN FINAL ---\n";
echo "INSERT ejecutados exitosamente : $insert_count\n";
echo "Errores                        : $error_count\n";
echo "Tablas actualizadas            : " . count($truncated_tables) . "\n";
echo "Tablas saltadas (no existen)   : " . count($skipped_tables) . "\n";
echo "Tablas protegidas (ignoradas)  : " . count($ignore_tables) . "\n";
echo "\nTablas actualizadas:\n";
foreach (array_keys($truncated_tables) as $t) {
    $count = DB::table($t)->count();
    echo "  - $t: $count registros\n";
}
