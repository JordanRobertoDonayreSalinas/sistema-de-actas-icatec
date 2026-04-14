<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$rows = \App\Models\Profesional::orderBy('id', 'desc')->limit(10)->get();
echo "Últimos 10 profesionales:\n";
foreach($rows as $r) {
    echo "- {$r->doc}: {$r->nombres} {$r->apellido_paterno} (Firma: {$r->tipo_firma}, Creado: {$r->created_at})\n";
}
