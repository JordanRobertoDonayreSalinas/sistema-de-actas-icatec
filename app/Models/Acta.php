<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'establecimiento_id',
        'responsable',
        'tema',
        'modalidad',
        'implementador',
        'firmado_pdf',
        'firmado',
    ];

    // Relaciones
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function acuerdos()
    {
        return $this->hasMany(Acuerdo::class);
    }

    public function observaciones()
    {
        return $this->hasMany(Observacion::class);
    }
}
