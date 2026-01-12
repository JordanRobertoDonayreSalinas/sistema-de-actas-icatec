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
        Schema::table('mon_modulo_citas', function (Blueprint $table) {
            // 1. AGREGAR NUEVAS COLUMNAS
            // Las colocamos después de personal_nombre para mantener orden
            $table->string('personal_correo', 100)->nullable()->after('personal_nombre');
            $table->string('personal_celular', 20)->nullable()->after('personal_correo');

            // Asegurarnos que existan las otras que mencionaste antes (por seguridad)
            // Si ya las creaste en la migración anterior, Laravel saltará esto, 
            // pero si no, es bueno verificar o comentarlas.
            // $table->string('personal_cargo', 100)->nullable()->after('personal_celular'); 
            // $table->string('utiliza_sihce', 2)->nullable()->after('firma_sihce');

            // 2. ELIMINAR COLUMNAS OBSOLETAS
            // Usamos un array para borrar varias a la vez
            $table->dropColumn([
                'capacitacion_otros_detalle',
                'firma_grafica'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_modulo_citas', function (Blueprint $table) {
            // Revertir cambios: Borrar las nuevas y restaurar las viejas

            $table->dropColumn(['personal_correo', 'personal_celular']);

            // Restauramos las columnas eliminadas (tipo text/string nullable)
            $table->string('capacitacion_otros_detalle', 255)->nullable();
            $table->longText('firma_grafica')->nullable();
        });
    }
};
