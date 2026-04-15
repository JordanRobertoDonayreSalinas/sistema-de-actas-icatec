<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$est = \Illuminate\Support\Facades\DB::table('establecimientos')->count();
$reni = \Illuminate\Support\Facades\DB::table('renipress_susalud_ica')->count();

echo "Establecimientos: $est\n";
echo "Renipress: $reni\n";
if ($est == 0) {
    echo "¡La tabla establecimientos está VACÍA!\n";
}
