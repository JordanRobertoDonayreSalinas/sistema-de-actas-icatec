<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoModulos extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'mon_monitoreo_modulos';

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'cabecera_monitoreo_id',
        'modulo_nombre',
        'contenido',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * * El 'contenido' se define como array para que Laravel convierta 
     * automáticamente el JSON de la base de datos a un array de PHP.
     */
    protected $casts = [
        'contenido' => 'array',
    ];

    /**
     * Relación inversa con la Cabecera del Monitoreo.
     * Un detalle de módulo pertenece a una única acta de monitoreo.
     */
    public function cabecera()
    {
        return $this->belongsTo(CabeceraMonitoreo::class, 'cabecera_monitoreo_id');
    }
}