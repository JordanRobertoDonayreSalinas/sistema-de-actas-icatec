<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$digitales = \App\Models\Profesional::where('tipo_firma', 'DIGITAL')->get();

echo "Verificando profesionales DIGITAL en com_dni:\n";
foreach($digitales as $p) {
    echo "- {$p->doc}: {$p->nombres} {$p->apellido_paterno}\n";
    $com_dni = \DB::table('com_dni')->where('profesional_id', $p->id)->first();
    if ($com_dni) {
        echo "  - Tipo DNI: {$com_dni->tip_dni}\n";
        echo "  - Versión: {$com_dni->version_dni}\n";
    } else {
        echo "  - No registrado en com_dni\n";
    }
}
