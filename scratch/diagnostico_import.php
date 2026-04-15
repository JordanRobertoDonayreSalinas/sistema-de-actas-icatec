<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$prod_file = 'produccion.sql';
$content = file_get_contents($prod_file);

preg_match_all('/CREATE TABLE `([^`]+)`/i', $content, $matches);
$prod_tables = array_unique($matches[1]);

$ignore_tables = ['incidencias', 'respuestas_incidencias', 'users', 'migrations', 'sessions', 'cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'password_reset_tokens'];

echo "Tablas en producción vs Conteo local:\n";
echo str_pad("Tabla", 30) . "| Local Count\n";
echo str_repeat("-", 45) . "\n";

foreach ($prod_tables as $table) {
    if (in_array($table, $ignore_tables)) continue;
    
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo str_pad($table, 30) . "| $count\n";
    } else {
        echo str_pad($table, 30) . "| NO EXISTE LOCALMENTE\n";
    }
}
