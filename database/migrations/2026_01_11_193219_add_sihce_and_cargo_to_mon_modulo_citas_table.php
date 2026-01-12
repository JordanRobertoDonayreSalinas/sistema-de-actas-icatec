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
            // Agregamos 'utiliza_sihce' (SI/NO) después de 'firma_sihce' (o donde prefieras)
            $table->string('utiliza_sihce', 2)->nullable()->after('firma_sihce');
            
            // Agregamos 'personal_cargo' después de 'personal_nombre'
            $table->string('personal_cargo', 100)->nullable()->after('personal_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_modulo_citas', function (Blueprint $table) {
            $table->dropColumn(['utiliza_sihce', 'personal_cargo']);
        });
    }
};
