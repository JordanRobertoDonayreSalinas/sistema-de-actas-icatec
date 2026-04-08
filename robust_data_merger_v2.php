<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$backupFile = 'backup_pro.sql';
echo "Iniciando Fusión Ultra-Robusta...\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$handle = fopen($backupFile, "r");
$buffer = "";
$inInsert = false;
$successCount = 0;
$failCount = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $trimmed = trim($line);
        
        // Detectar inicio de INSERT (con o sin backticks)
        if (preg_match('/^INSERT INTO/i', $trimmed)) {
            $inInsert = true;
            $buffer = $line;
        } elseif ($inInsert) {
            $buffer .= $line;
        }

        // Detectar fin de sentencia
        if ($inInsert && str_ends_with($trimmed, ';')) {
            try {
                DB::unprepared($buffer);
                $successCount++;
                if ($successCount % 50 == 0) echo "Bloques insertados: $successCount...\n";
            } catch (\Exception $e) {
                $failCount++;
                // Extraer nombre de tabla para el log
                preg_match('/INSERT INTO `?([^` ]+)`?.*/i', $buffer, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "Error en bloque para '$tableName': " . substr($e->getMessage(), 0, 100) . "\n";
            }
            $buffer = "";
            $inInsert = false;
        }
    }
    fclose($handle);
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n--- RESULTADO FINAL ---\n";
echo "Exitosos: $successCount\n";
echo "Fallidos: $failCount\n";
echo "RECUERDA: Los fallos son normales si las columnas difieren; los datos compatibles se cargaron.\n";
