<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$dni = '71883058';
$pass = '14StinGraY95';

echo "Configurando usuario administrador $dni...\n";

$user = User::where('username', $dni)->first();

if (!$user) {
    echo "Usuario no encontrado en la data importada. Creándolo...\n";
    $user = new User();
    $user->username = $dni;
    $user->name = 'ADMINISTRADOR';
}

$user->password = Hash::make($pass);
$user->role = 'admin';
$user->status = 'active';

if ($user->save()) {
    echo "¡ÉXITO! Usuario $dni configurado como admin con la contraseña proporcionada.\n";
} else {
    echo "ERROR al guardar el usuario.\n";
}
