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
     * Como tu tabla ya tiene una primaria y Laravel no la encuentra, 
     * definimos 'doc' como la llave. Si 'doc' no es la única llave, 
     * desactivamos el incremento para que Laravel no busque un 'id' inexistente.
     */
    protected $primaryKey = 'doc'; 
    public $incrementing = false;
    protected $keyType = 'string';

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
     * Un integrante pertenece a una única acta/cabecera.
     */
    public function cabecera()
    {
        return $this->belongsTo(CabeceraMonitoreo::class, 'cabecera_monitoreo_id');
    }
}