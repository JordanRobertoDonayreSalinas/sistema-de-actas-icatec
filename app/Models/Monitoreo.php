<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoreo extends Model
{
    use HasFactory;

    protected $table = 'monitoreos';

    /**
     * Atributos asignables masivamente.
     * Cabecera simplificada: Solo datos de identificación y control.
     */
    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'fecha',
        'responsable',
        'implementador',
        'firmado',
        'firmado_pdf',
    ];

    /**
     * Relación con el Usuario (Monitor) que crea el acta.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el Establecimiento de Salud monitoreado.
     */
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    /**
     * NUEVA RELACIÓN: Equipo de Trabajo / Acompañantes.
     * Relaciona la cabecera con la tabla independiente de acompañantes.
     */
    public function equipo()
    {
        return $this->hasMany(MonitoreoEquipo::class, 'monitoreo_id');
    }

    /**
     * Relación con el Módulo 01: Programación de Consultorios.
     */
    public function programacion()
    {
        return $this->hasOne(MonitoreoProgramacion::class, 'monitoreo_id');
    }

    /**
     * Relación con Participantes del EESS (Personal del establecimiento).
     */
    public function participantes()
    {
        return $this->hasMany(Participante::class, 'monitoreo_id');
    }

    /**
     * Relación con detalles genéricos (para módulos que aún no tienen tabla propia).
     */
    public function detalles()
    {
        return $this->hasMany(MonitoreoDetalle::class, 'monitoreo_id');
    }
}