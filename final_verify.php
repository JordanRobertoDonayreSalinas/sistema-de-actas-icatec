<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- VERIFICACIÓN FINAL ---\n";

$tables = ['users', 'mon_cabecera_monitoreo', 'incidencias'];

foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        $count = DB::table($t)->count();
        echo "Tabla '$t': EXISTE con $count registros.\n";
        
        if ($t === 'users') {
            $admin = DB::table('users')->where('username', '71883058')->first();
            if ($admin) {
                echo "  - Usuario 71883058: ENCONTRADO (Role: {$admin->role}, Status: {$admin->status})\n";
            } else {
                echo "  - Usuario 71883058: NO ENCONTRADO\n";
            }
        }
    } else {
        echo "Tabla '$t': NO EXISTE\n";
    }
}
