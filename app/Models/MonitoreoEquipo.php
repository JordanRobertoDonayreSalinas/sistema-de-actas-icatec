<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoEquipo extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'mon_equipo_monitoreo';

    /**
     * CONFIGURACIÓN DE LLAVE PRIMARIA
     * Tras la migración, la llave primaria es 'id' (por defecto en Laravel).
     * Por lo tanto, ELIMINAMOS o comentamos las líneas anteriores que apuntaban a 'doc'.
     */
    // protected $primaryKey = 'doc'; <-- ELIMINADO
    // public $incrementing = false; <-- ELIMINADO
    // protected $keyType = 'string'; <-- ELIMINADO

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'cabecera_monitoreo_id',
        'tipo_doc',
        'doc',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'cargo',
        'institucion'
    ];

    /**
     * Relación con la Cabecera del Monitoreo.
     */
    public function cabecera()
    {
        return $this->belongsTo(CabeceraMonitoreo::class, 'cabecera_monitoreo_id');
    }
}