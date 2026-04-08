<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$backupFile = 'backup_pro.sql';
echo "Diagnóstico de Fusión...\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

$handle = fopen($backupFile, "r");
$buffer = "";
$inInsert = false;
$count = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $trimmed = trim($line);
        
        if (preg_match('/^INSERT INTO\s+`?([a-z0-9_]+)`?/i', $trimmed, $m)) {
            $inInsert = true;
            $tableName = $m[1];
            $buffer = $line;
            // echo "Detectado INSERT para tabla: $tableName\n";
        } elseif ($inInsert) {
            $buffer .= $line;
        }

        if ($inInsert && preg_match('/;\s*$/', $trimmed)) {
            try {
                DB::unprepared($buffer);
                $count++;
            } catch (\Exception $e) {
                echo "Error en $tableName: " . substr($e->getMessage(), 0, 50) . "\n";
            }
            $buffer = "";
            $inInsert = false;
        }
    }
    fclose($handle);
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Total de bloques insertados: $count\n";
