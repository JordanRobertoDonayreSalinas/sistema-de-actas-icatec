<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipoComputo extends Model
{
    protected $table = 'mon_equipos_computo';

    protected $fillable = [
        'cabecera_monitoreo_id',
        'modulo',
        'descripcion',
        'cantidad',
        'estado',
        'propio'
    ];

    // Relación inversa: Un equipo pertenece a un monitoreo
    public function monitoreo()
    {
        return $this->belongsTo(Monitoreo::class, 'cabecera_monitoreo_id');
    }
}