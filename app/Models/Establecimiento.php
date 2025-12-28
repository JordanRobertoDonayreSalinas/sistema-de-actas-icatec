<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Establecimiento extends Model
{
    use HasFactory;

    protected $table = 'establecimientos';

    /**
     * Atributos asignables masivamente.
     * IMPORTANTE: He añadido 'categoria' que faltaba.
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'distrito',
        'provincia',
        'microred',
        'red',
        'responsable',
        'categoria' // <--- ESTO FALTABA PARA PODER ACTUALIZARLO
    ];

    public $timestamps = false;

    /**
     * Relación con las Actas de Monitoreo (Sistema Profesional)
     * Permite acceder a todos los monitoreos realizados a este establecimiento.
     */
    public function monitoreos()
    {
        return $this->hasMany(CabeceraMonitoreo::class, 'establecimiento_id');
    }

    /**
     * Relación con las Actas de Asistencia Técnica (Listado Simple)
     */
    public function actas()
    {
        return $this->hasMany(Acta::class, 'establecimiento_id');
    }
}