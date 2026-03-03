<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$establecimiento = App\Models\Establecimiento::where('nombre', 'like', '%SUNAMPE%')->first();

if ($establecimiento) {
    echo "Establecimiento: " . $establecimiento->nombre . "\n";
    $cabeceras = App\Models\CabeceraMonitoreo::where('establecimiento_id', $establecimiento->id)->get();

    foreach ($cabeceras as $c) {
        $modulos = App\Models\MonitoreoModulos::where('cabecera_monitoreo_id', $c->id)
            ->where('modulo_nombre', 'like', '%psicologia%')
            ->get();

        foreach ($modulos as $m) {
            echo "==== CABECERA: " . $c->id . " ====\n";
            $contenido = $m->contenido;

            echo "TIPO: " . ($contenido['tipo_conectividad'] ?? 'NO_TIPO') . "\n";
            echo "FUENTE: " . ($contenido['wifi_fuente'] ?? 'NO_FUENTE') . "\n";
            echo "OPERADOR: " . ($contenido['operador_servicio'] ?? 'NO_OPERADOR') . "\n";

            // Testing Helper:
            $con = \App\Helpers\ModuloHelper::getConectividadActa($c, 'consulta_psicologia');
            echo "Helper directo: ";
            print_r($con);

            $con2 = \App\Helpers\ModuloHelper::getConectividadActa($c, 'Consulta Externa: Psicología');
            echo "Helper indirecto: ";
            print_r($con2);

        }
    }
}
