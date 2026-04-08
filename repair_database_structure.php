<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Iniciando reparación de estructura...\n";

// 1. Asegurar tabla Incidencias
if (!Schema::hasTable('incidencias')) {
    echo "Creando tabla 'incidencias' por migración manual...\n";
    try {
        $m = require 'database/migrations/2026_03_25_150000_create_incidencias_table.php';
        $m->up();
    } catch (\Exception $e) { echo "Aviso en incidencias: " . $e->getMessage() . "\n"; }
}

// 2. Asegurar tablas Monitoreo
if (!Schema::hasTable('mon_cabecera_monitoreo')) {
    echo "Restaurando tablas de Monitoreo...\n";
    // Como las migraciones de monitoreo tienen renames complejos, las crearemos quirúrgicamente si no existen
    try {
        if (!Schema::hasTable('monitoreos')) {
           $m1 = require 'database/migrations/2025_12_26_120434_create_monitoreos_table.php';
           $m1->up();
        }
        Schema::rename('monitoreos', 'mon_cabecera_monitoreo');
    } catch (\Exception $e) { echo "Aviso en monitoreo: " . $e->getMessage() . "\n"; }
}

// 3. Agregar columnas de fotos perdidas (IMPORTANTE para cumplir lo pedido por el usuario anteriormente)
$tablesWithFotos = [
    'citas_actas', 'emergencia_actas', 'farmacia_actas', 'fua_actas', 'ges_adm_actas',
    'laboratorio_actas', 'medicina_actas', 'mental_actas', 'nutricion_actas',
    'odontologia_actas', 'psicologia_actas', 'referencias_actas', 'triaje_actas',
];

foreach ($tablesWithFotos as $t) {
    if (Schema::hasTable($t)) {
        Schema::table($t, function (Blueprint $table) use ($t) {
            if (!Schema::hasColumn($t, 'foto1')) {
                $table->string('foto1')->nullable();
                echo "- Agregada foto1 a $t\n";
            }
            if (!Schema::hasColumn($t, 'foto2')) {
                $table->string('foto2')->nullable();
                echo "- Agregada foto2 a $t\n";
            }
        });
    }
}

// 4. Asegurar otras tablas de monitoreo
$migrationsToEnsure = [
    'database/migrations/2025_12_27_132101_create_mon_respuesta_entrevistado_table.php',
    'database/migrations/2025_12_28_112748_create_mon_monitoreo_modulos_table.php',
    'database/migrations/2026_03_25_150001_create_respuestas_incidencias_table.php',
    'database/migrations/2026_03_31_210000_create_croquis_colaboracion_table.php'
];

foreach ($migrationsToEnsure as $path) {
    try {
        $m = require $path;
        $m->up();
        echo "Ejecutada migración: $path\n";
    } catch (\Exception $e) {
        // Ignorar si ya existe
    }
}

echo "Reparación de estructura FINALIZADA.\n";
