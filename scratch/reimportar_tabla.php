<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// --- TABLAS ESPECÍFICAS A REIMPORTAR ---
$target_tables = ['establecimientos'];

$prod_file = 'produccion.sql';

$handle = fopen($prod_file, 'r');
if (!$handle) die("No se puede abrir produccion.sql\n");

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::statement("SET SESSION sql_mode=''");  // Desactivar modo estricto para permitir '0000-00-00'

$current_table = null;
$current_statement = '';
$truncated_tables = [];
$insert_count = 0;
$error_count = 0;

while (($line = fgets($handle)) !== false) {
    $line_trimmed = trim($line);

    // Detectar inicio de INSERT INTO de la tabla objetivo
    if (preg_match('/^INSERT INTO `([^`]+)`/i', $line_trimmed, $m)) {
        $table_name = $m[1];

        // Solo procesar las tablas objetivo
        if (in_array($table_name, $target_tables)) {
            $current_table = $table_name;

            // Vaciar la tabla si es la primera vez
            if (!isset($truncated_tables[$table_name])) {
                echo "Limpiando tabla: $table_name\n";
                DB::table($table_name)->truncate();
                $truncated_tables[$table_name] = true;
            }
        } else {
            $current_table = null;
        }
    }

    // Acumular el statement si estamos dentro de una tabla objetivo
    if ($current_table !== null) {
        $current_statement .= $line;

        // Si la línea termina el statement
        if (rtrim($line) !== '' && substr(rtrim($line), -1) === ';') {
            echo "Ejecutando INSERT en $current_table...\n";
            try {
                DB::unprepared($current_statement);
                $insert_count++;
                echo "  [OK]\n";
            } catch (\Exception $e) {
                $error_count++;
                echo "  [ERROR]: " . $e->getMessage() . "\n";
            }
            $current_statement = '';
            $current_table = null;
        }
    }
}

fclose($handle);
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Verificar resultado
foreach ($target_tables as $t) {
    $count = DB::table($t)->count();
    echo "\nResultado final: $t = $count registros\n";
}

echo "\nINSERTs ejecutados: $insert_count | Errores: $error_count\n";
