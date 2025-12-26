<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',            // ID del usuario que crea el acta
        'fecha',
        'establecimiento_id',
        'responsable',
        'tema',
        'modalidad',
        'implementador',
        'tipo',               // 'asistencia' o 'monitoreo'
        'firmado_pdf',
        'firmado',
        // Columnas para las 5 imágenes
        'imagen1',
        'imagen2',
        'imagen3',
        'imagen4',
        'imagen5',
        // Si vas a usar los campos de monitoreo en esta misma tabla:
        'hallazgos',
        'acuerdos_monitoreo' 
    ];

    /**
     * Relación con el Usuario (Creador del acta)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el Establecimiento
     */
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    /**
     * Relación con los Participantes
     */
    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    /**
     * Relación con las Actividades (Solo para Asistencia Técnica)
     */
    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    /**
     * Relación con los Acuerdos (Solo para Asistencia Técnica)
     */
    public function acuerdos()
    {
        return $this->hasMany(Acuerdo::class);
    }

    /**
     * Relación con las Observaciones
     */
    public function observaciones()
    {
        return $this->hasMany(Observacion::class);
    }
}