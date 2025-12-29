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
        'categoria_congelada',
        'responsable_congelado',
        'fecha',
        'responsable',
        'implementador',
        'firmado',
        'firmado_pdf'
    ];

    /**
     * Relación con el establecimiento.
     * Un monitoreo pertenece a un establecimiento de salud.
     */
    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }

    /**
     * Relación con el equipo de monitoreo.
     * Un acta puede tener varios miembros del equipo evaluador.
     */
    public function equipo(): HasMany
    {
        return $this->hasMany(MonitoreoEquipo::class, 'cabecera_monitoreo_id');
    }

    /**
     * RELACIÓN VITAL: Detalles de los módulos.
     * Aquí se guarda la configuración de módulos activos y las rutas de los PDF firmados.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(MonitoreoModulos::class, 'cabecera_monitoreo_id');
    }

    /**
     * Relación con el usuario que creó el registro.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}