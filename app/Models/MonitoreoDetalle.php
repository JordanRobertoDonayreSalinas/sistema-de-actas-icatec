<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoreoDetalle extends Model
{
    use HasFactory;

    protected $table = 'monitoreo_detalles';

    protected $fillable = [
        'monitoreo_id', // Correcto: Referencia a la nueva tabla monitoreos
        'modulo_nombre',
        'contenido',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'contenido' => 'array', // Correcto: Permite manejar el JSON como un array de PHP
    ];

    /**
     * Relación Inversa: Un detalle pertenece a una cabecera de Monitoreo.
     */
    public function monitoreo()
    {
        // Correcto: Se vincula al nuevo modelo Monitoreo
        // Se especifica 'monitoreo_id' para evitar ambigüedades tras la migración
        return $this->belongsTo(Monitoreo::class, 'monitoreo_id');
    }
}