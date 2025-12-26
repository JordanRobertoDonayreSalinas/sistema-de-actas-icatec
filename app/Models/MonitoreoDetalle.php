<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoDetalle extends Model
{
    use HasFactory;

    protected $table = 'monitoreo_detalles';

    protected $fillable = [
        'monitoreo_id', // CAMBIADO: Antes era acta_id
        'modulo_nombre',
        'contenido',
    ];

    protected $casts = [
        'contenido' => 'array',
    ];

    /**
     * Relación Inversa: Un detalle pertenece a una cabecera de Monitoreo.
     */
    public function monitoreo()
    {
        // CAMBIADO: Ahora pertenece al nuevo modelo Monitoreo
        return $this->belongsTo(Monitoreo::class, 'monitoreo_id');
    }
}