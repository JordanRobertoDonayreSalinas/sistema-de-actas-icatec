<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabeceraMonitoreo extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'mon_cabecera_monitoreo';

    /**
     * Atributos asignables masivamente.
     * SE AGREGÓ: 'categoria_congelada'
     */
    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'fecha',
        'responsable',
        'categoria_congelada', // <--- FUNDAMENTAL PARA EL HISTÓRICO
        'implementador',
        'firmado',
        'firmado_pdf',
    ];

    /**
     * MUTATOR PARA IMPLEMENTADOR
     */
    public function setImplementadorAttribute($value)
    {
        $this->attributes['implementador'] = ucwords(mb_strtolower($value, 'UTF-8'));
    }

    /**
     * Relación con el Usuario (Monitor)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el Establecimiento de Salud
     */
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    /**
     * Relación con el Equipo de Monitoreo
     */
    public function equipo()
    {
        return $this->hasMany(MonitoreoEquipo::class, 'cabecera_monitoreo_id');
    }

    // ... resto de relaciones iguales ...
    
    public function equiposComputo()
    {
        return $this->hasMany(EquipoComputo::class, 'cabecera_monitoreo_id');
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