<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ARREGLO CON LOS NOMBRES DE LAS TABLAS A MODIFICAR
        $tablas = ['mon_modulo_prenatal', 'mon_modulo_parto'];

        foreach ($tablas as $nombreTabla) {
            Schema::table($nombreTabla, function (Blueprint $table) {
                // 1. AGREGAR NUEVAS COLUMNAS
                // Las insertamos después de 'personal_nombre' para mantener el orden lógico
                $table->string('personal_cargo', 100)->nullable()->after('personal_nombre');
                $table->string('personal_correo', 100)->nullable()->after('personal_cargo');
                $table->string('personal_celular', 20)->nullable()->after('personal_correo');

                // Agregamos también utiliza_sihce ya que lo pusimos en el formulario
                $table->string('utiliza_sihce', 2)->nullable()->after('firma_sihce');

                // 2. ELIMINAR COLUMNAS OBSOLETAS
                // Usamos un array para borrar varias a la vez. 
                // Verificamos si existen antes de borrarlas para evitar errores si ya se borraron.
                $columnstodrop = [];

                // Ajusta los nombres exactos según tu BD actual (ej. singular o plural)
                $columnstodrop[] = 'capacitacion_otros_detalle';
                $columnstodrop[] = 'materiales_otros';
                $columnstodrop[] = 'firma_grafica';

                $table->dropColumn($columnstodrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablas = ['mon_modulo_prenatal', 'mon_modulo_parto'];

        foreach ($tablas as $nombreTabla) {
            Schema::table($nombreTabla, function (Blueprint $table) {
                // 1. ELIMINAR LAS NUEVAS
                $table->dropColumn([
                    'personal_cargo',
                    'personal_correo',
                    'personal_celular',
                    'utiliza_sihce'
                ]);

                // 2. RESTAURAR LAS BORRADAS (Como backup)
                $table->string('capacitacion_otros_detalle', 191)->nullable();
                $table->string('materiales_otros', 191)->nullable();
                $table->longText('firma_grafica')->nullable();
            });
        }
    }
};
