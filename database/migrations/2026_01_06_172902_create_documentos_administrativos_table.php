<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('documentos_administrativos', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');
        $table->foreignId('establecimiento_id')->constrained('establecimientos');
        
        // Datos del Profesional
        $table->string('profesional_tipo_doc', 20); // DNI, CEX
        $table->string('profesional_doc', 20);
        $table->string('profesional_nombre');
        $table->string('profesional_apellido_paterno');
        $table->string('profesional_apellido_materno');
        
        // Datos del Cargo y Sistemas de acceso mencionados en tus documentos
        $table->string('cargo_rol');
        $table->string('area_oficina');
        $table->text('sistemas_acceso'); // Para guardar los módulos seleccionados
        $table->string('correo_electronico')->nullable();
        
        // Control de Formatos y Archivos
        $table->enum('tipo_formato', ['Compromiso', 'DeclaracionJurada']);
        $table->string('pdf_generado_path')->nullable();
        $table->string('pdf_firmado_path')->nullable(); 
        
        $table->foreignId('user_id')->constrained('users'); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_administrativos');
    }
};
