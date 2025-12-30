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
        'responsable_congelado'
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
     * RELACIÓN CORREGIDA: Detalles de los módulos.
     * Cambiado de MonitoreoDetalle a MonitoreoModulos para coincidir con tu sistema.
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
     */
    public function getProgresoAttribute()
    {
        $totalModulos = 8; // Ajustar según la cantidad real de módulos activos
        $completados = $this->detalles()->count();
        return ($totalModulos > 0) ? ($completados / $totalModulos) * 100 : 0;
    }
}