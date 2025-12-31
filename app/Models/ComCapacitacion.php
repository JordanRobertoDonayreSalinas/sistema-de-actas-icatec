<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Profesional;
use App\Models\CabeceraMonitoreo;

class ComCapacitacion extends Model
{
    /**
     * Definimos explícitamente la tabla porque Laravel buscaría 'com_capacitacions'
     */
    protected $table = 'com_capacitacion';

    /**
     * Campos permitidos para asignación masiva.
     * Estos deben coincidir con las claves que usas en el updateOrCreate del controlador.
     */
    protected $fillable = [
        'acta_id',
        'modulo_id',
        'profesional_id',
        'recibieron_cap',
        'institucion_cap',
    ];

    /**
     * =========================================================================
     * RELACIONES
     * =========================================================================
     */

    /**
     * Relación con el Acta (Tabla: mon_cabecera_monitoreo)
     */
    public function acta()
    {
        // El segundo parámetro es la FK en esta tabla ('acta_id')
        // El tercer parámetro es la PK en la tabla de actas ('id')
        return $this->belongsTo(CabeceraMonitoreo::class, 'acta_id', 'id');
    }

    /**
     * Relación con el Profesional (Tabla: mon_profesionales)
     */
    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id', 'id');
    }
}
