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
        // 1. Tabla Prenatal
        Schema::table('mon_modulo_prenatal', function (Blueprint $table) {
            // Agregamos nullable por si ya tienes datos previos
            $table->date('fecha_registro')->nullable()->after('monitoreo_id');
        });

        // 2. Tabla Parto
        Schema::table('mon_modulo_parto', function (Blueprint $table) {
            $table->date('fecha_registro')->nullable()->after('monitoreo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_modulo_prenatal', function (Blueprint $table) {
            $table->dropColumn('fecha_registro');
        });

        Schema::table('mon_modulo_parto', function (Blueprint $table) {
            $table->dropColumn('fecha_registro');
        });
    }
};
