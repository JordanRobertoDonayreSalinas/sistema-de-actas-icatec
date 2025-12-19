<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id(); // Este es vital para la relación con actas
            $table->string('codigo')->nullable();      // Ej: 3372
            $table->string('nombre')->nullable();      // Ej: PACHACUTEC
            $table->string('provincia')->nullable();   // Ej: ICA
            $table->string('distrito')->nullable();    // Ej: PACHACUTEC
            $table->string('categoria')->nullable();   // Ej: I-3
            $table->string('red')->nullable();         // Ej: ICA-PALPA-NAZCA
            $table->string('microred')->nullable();    // Ej: PUEBLO NUEVO
            $table->string('responsable')->nullable(); // Ej: ROGER VIDAL...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};