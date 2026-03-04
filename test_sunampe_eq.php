<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cab = App\Models\CabeceraMonitoreo::where('establecimiento_id', 116)->first();
$eqs = App\Models\EquipoComputo::where('cabecera_monitoreo_id', $cab->id)->get();

foreach ($eqs as $e) {
    echo "Equipo desc: " . $e->descripcion;
    echo " Modulo guardado: " . $e->modulo . "\n";
    $con = App\Helpers\ModuloHelper::getConectividadActa($cab, $e->modulo);
    echo "Conectividad (Helper): ";
    print_r($con);
    echo "---\n";
}
