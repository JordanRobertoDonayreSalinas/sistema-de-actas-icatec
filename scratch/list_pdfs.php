<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- ACTAS --- \n";
$actas = \App\Models\Acta::whereNotNull('firmado_pdf')->orderBy('id','desc')->limit(3)->get();
foreach($actas as $a) {
    echo "ID: {$a->id} | PDF: {$a->firmado_pdf} | " . (file_exists(storage_path('app/public/'.$a->firmado_pdf)) ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n--- MONITOREO --- \n";
$monitoreos = \App\Models\CabeceraMonitoreo::whereNotNull('firmado_pdf')->orderBy('id','desc')->limit(3)->get();
foreach($monitoreos as $m) {
    echo "ID: {$m->id} | PDF: {$m->firmado_pdf} | " . (file_exists(storage_path('app/public/'.$m->firmado_pdf)) ? 'EXISTS' : 'MISSING') . "\n";
}

echo "\n--- IMPLEMENTACION --- \n";
// Since I don't know the exact models for implementation in this context easily (they are dynamic), I'll check a few common ones
$impModels = [
    \App\Models\Implementacion\MedicinaActa::class,
    \App\Models\Implementacion\PsicologiaActa::class
];
foreach($impModels as $model) {
    if (class_exists($model)) {
        $acts = $model::whereNotNull('archivo_pdf')->orderBy('id','desc')->limit(3)->get();
        echo "Model: $model\n";
        foreach($acts as $a) {
             echo "ID: {$a->id} | PDF: {$a->archivo_pdf} | " . (file_exists(storage_path('app/public/'.$a->archivo_pdf)) ? 'EXISTS' : 'MISSING') . "\n";
        }
    }
}
