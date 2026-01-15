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
            // Agregamos el campo tipo DATE, nullable por si ya tienes registros
            $table->date('fecha_registro')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_modulo_citas', function (Blueprint $table) {
            //
        });
    }
};
