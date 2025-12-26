<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoProgramacion extends Model
{
    use HasFactory;

    protected $table = 'monitoreo_programacion';

    /**
     * Los atributos que son asignables masivamente.
     * Se han agregado todos los campos técnicos del formulario profesional.
     */
    protected $fillable = [
        'monitoreo_id',
        
        // Sección 1: Responsable RRHH
        'rrhh_nombre',
        'rrhh_dni',
        'rrhh_telefono',
        'rrhh_correo',
        
        // Sección 2: Acceso y Lógica condicional
        'odoo', // SI/NO
        'quien_programa_nombre',
        'quien_programa_dni',
        'quien_programa_telefono',
        'quien_programa_correo',
        
        // Sección 3: Capacitación y Mes
        'capacitacion',
        'mes_sistema',
        
        // Sección 4: Tabla de Servicios (Turnos y Cupos)
        'servicios', 
        
        // Sección 5: Cierre y Firma
        'comentarios',
        'entrevistado_nombre'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Convertimos 'servicios' a array para manejar la tabla de cupos fácilmente.
     */
    protected $casts = [
        'servicios' => 'array',
    ];

    /**
     * Relación Inversa: Un registro de programación pertenece a un Monitoreo (Cabecera).
     */
    public function monitoreo()
    {
        return $this->belongsTo(Monitoreo::class, 'monitoreo_id');
    }
}