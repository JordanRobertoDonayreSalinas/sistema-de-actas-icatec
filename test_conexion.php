<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$establecimiento = App\Models\Establecimiento::where('nombre', 'like', '%SUNAMPE%')->first();

if ($establecimiento) {
    echo "Establecimiento encontrado: " . $establecimiento->nombre . " (ID: " . $establecimiento->id . ")\n";
    $cabeceras = App\Models\CabeceraMonitoreo::where('establecimiento_id', $establecimiento->id)->get();

    foreach ($cabeceras as $cabecera) {
        $modulos = App\Models\MonitoreoModulos::where('cabecera_monitoreo_id', $cabecera->id)
            ->where('modulo_nombre', 'like', '%Psicolo%')
            ->get();

        foreach ($modulos as $modulo) {
            echo "--- Cabecera ID: {$cabecera->id}, Modulo ID: {$modulo->id} ---\n";
            echo "--- JSON Modulo --- \n";
            echo $modulo->contenido . "\n";
            echo "------------------- \n";

            $equipos = App\Models\EquipoComputo::where('cabecera_monitoreo_id', $cabecera->id)
                ->where('modulo', 'like', '%Psicolo%')
                ->get();

            foreach ($equipos as $equipo) {
                echo "Equipo ID: {$equipo->id} - Modulo guardado en equipo: {$equipo->modulo}\n";
            }
        }
    }
} else {
    echo "Establecimiento no encontrado.\n";
}
