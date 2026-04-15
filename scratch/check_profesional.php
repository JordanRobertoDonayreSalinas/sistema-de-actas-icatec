<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dni = '71883060';
$p = \App\Models\Profesional::where('doc', $dni)->first();

if ($p) {
    echo "Profesional encontrado:\n";
    echo "- ID: {$p->id}\n";
    echo "- Documento: {$p->doc}\n";
    echo "- Nombre: {$p->nombres} {$p->apellido_paterno} {$p->apellido_materno}\n";
    echo "- Tipo Firma: {$p->tipo_firma}\n";
    
    $com_dni = \DB::table('com_dni')->where('profesional_id', $p->id)->first();
    if ($com_dni) {
        echo "- Tipo DNI (com_dni): {$com_dni->tip_dni}\n";
        echo "- Versión DNI: {$com_dni->version_dni}\n";
    } else {
        echo "- No se encontró registro en 'com_dni'\n";
    }
} else {
    echo "Profesional con DNI $dni no encontrado.\n";
}
