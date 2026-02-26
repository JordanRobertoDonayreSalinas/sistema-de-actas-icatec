<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$est = DB::table('establecimientos')->first();
file_put_contents('dump_est.json', json_encode($est, JSON_PRETTY_PRINT));
echo "OK";
