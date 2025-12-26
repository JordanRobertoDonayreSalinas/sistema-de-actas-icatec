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
    Schema::create('monitoreo_programacion', function (Blueprint $table) {
        $table->id();
        // Relación con la cabecera de monitoreo
        $table->foreignId('monitoreo_id')->constrained('monitoreos')->onDelete('cascade');
        
        // Campos específicos detectados en tu formulario actual
        $table->integer('consultorios_programados')->default(0);
        $table->integer('consultorios_que_cumplen')->default(0);
        $table->string('turno_mañana')->nullable();
        $table->string('turno_tarde')->nullable();
        $table->text('observaciones_programacion')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoreo_programacion');
    }
};
