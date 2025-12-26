<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
    Schema::create('monitoreo_detalles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('acta_id')->constrained('actas')->onDelete('cascade');
        $table->string('nombre_modulo'); // Ejemplo: 'SIS/HIS', 'Farmacia', 'RRHH'
        $table->json('contenido'); // Aquí guardamos los checks y datos específicos del módulo
        $table->text('comentarios')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoreo_detalles');
    }
};
