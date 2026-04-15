<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$total = \App\Models\Profesional::count();
$conFirma = \App\Models\Profesional::whereNotNull('firma_path')->count();
$digital = \App\Models\Profesional::where('tipo_firma', 'DIGITAL')->count();

echo "Total profesionales registrados: $total\n";
echo "Profesionales con imagen de firma (manual): $conFirma\n";
echo "Profesionales marcados como DIGITAL: $digital\n";

$digitales = \App\Models\Profesional::where('tipo_firma', 'DIGITAL')->get();
echo "\nProfesionales con firma DIGITAL:\n";
foreach($digitales as $p) {
    echo "- {$p->doc}: {$p->apellido_paterno} {$p->nombres} (Tipo: {$p->tipo_firma})\n";
}
