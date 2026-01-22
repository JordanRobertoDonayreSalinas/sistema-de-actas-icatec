<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CabeceraMonitoreo extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'mon_cabecera_monitoreo';

    /**
     * Atributos asignables masivamente.
     * IMPORTANTE: Se agregaron 'tipo_origen' y 'numero_acta' para permitir la numeración independiente.
     */
    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'fecha',
        'responsable',
        'implementador',
        'firmado',
        'firmado_pdf',
        'categoria_congelada',
        'responsable_congelado',
        'foto1',
        'foto2',
        
        // CAMPOS NUEVOS PARA LA LÓGICA DE SERIES
        'tipo_origen', // 'ESTANDAR' o 'ESPECIALIZADA'
        'numero_acta'  // Correlativo (1, 2, 3...)
    ];

    /**
     * Relación con el establecimiento de salud.
     */
    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }

    /**
     * Relación con el equipo de monitoreo / personal.
     */
    public function equipo(): HasMany
    {
        return $this->hasMany(MonitoreoEquipo::class, 'cabecera_monitoreo_id');
    }

    /**
     * Detalles de los módulos.
     * Coincide con el modelo MonitoreoModulos de tu sistema.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(MonitoreoModulos::class, 'cabecera_monitoreo_id');
    }

    /**
     * Relación con el usuario del sistema que creó el registro.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accesor para obtener el progreso del monitoreo.
     * CORREGIDO: Calcula el porcentaje dinámicamente según si es CSMC (3 módulos) o IPRESS (18 módulos).
     */
    public function getProgresoAttribute()
    {
        // Si es ESPECIALIZADA son 3 módulos (Citas, Triaje, Acogida)
        // Si es ESTANDAR son 18 módulos
        $totalModulos = ($this->tipo_origen === 'ESPECIALIZADA') ? 3 : 18;
        
        $completados = $this->detalles()
                            ->where('modulo_nombre', '!=', 'config_modulos')
                            ->count();
        
        return ($totalModulos > 0) ? round(($completados / $totalModulos) * 100) : 0;
    }
}