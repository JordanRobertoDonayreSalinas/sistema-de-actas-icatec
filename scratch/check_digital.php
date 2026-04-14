<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$digitales = \App\Models\Profesional::where('tipo_firma', 'DIGITAL')
    ->orderBy('updated_at', 'desc')
    ->get();

echo "Profesionales con firma DIGITAL (DNI Electrónico):\n";
foreach($digitales as $p) {
    echo "- {$p->doc}: {$p->apellido_paterno} {$p->nombres} (Actualizado: {$p->updated_at})\n";
}
