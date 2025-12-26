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
    Schema::table('monitoreo_detalles', function (Blueprint $table) {
        // Eliminamos la relación antigua con actas
        $table->dropForeign(['acta_id']);
        $table->dropColumn('acta_id');
        
        // Creamos la nueva relación con la tabla monitoreos
        $table->foreignId('monitoreo_id')->after('id')->constrained('monitoreos')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
