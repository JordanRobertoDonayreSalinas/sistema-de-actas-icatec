<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabeceraMonitoreo extends Model
{
    use HasFactory;

    protected $table = 'mon_cabecera_monitoreo';

    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'fecha',
        'responsable',
        'categoria_congelada',
        'implementador',
        'firmado',
        'firmado_pdf',
    ];

    public function setImplementadorAttribute($value)
    {
        $this->attributes['implementador'] = ucwords(mb_strtolower($value, 'UTF-8'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function equipo()
    {
        return $this->hasMany(MonitoreoEquipo::class, 'cabecera_monitoreo_id');
    }

    /**
     * RELACIÓN PARA EQUIPOS DE CÓMPUTO
     * Definimos 'equipos' como alias para facilitar el llamado en las vistas modulares
     */
    public function equipos()
    {
        return $this->hasMany(EquipoComputo::class, 'cabecera_monitoreo_id');
    }

    public function equiposComputo() 
    {
        return $this->equipos();
    }

    public function respuestasEntrevistas()
    {
        return $this->hasMany(RespuestaEntrevistado::class, 'cabecera_monitoreo_id');
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'cabecera_monitoreo_id');
    }
}