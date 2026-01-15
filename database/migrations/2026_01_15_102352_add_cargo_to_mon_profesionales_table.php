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
        Schema::table('mon_profesionales', function (Blueprint $table) {
            // Agregamos el campo 'cargo'. 
            // Lo ponemos 'nullable' para que no de error si ya tienes datos registrados.
            // 'after' es opcional, sirve para ordenar la columna visualmente en la BD.
            $table->string('cargo')->nullable()->after('email'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_profesionales', function (Blueprint $table) {
            // Eliminamos el campo si revertimos la migración
            $table->dropColumn('cargo');
        });
    }
};