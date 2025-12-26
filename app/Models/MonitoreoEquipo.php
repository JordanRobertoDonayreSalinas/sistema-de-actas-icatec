<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoreoEquipo extends Model
{
    protected $table = 'monitoreo_equipo';

    protected $fillable = [
        'monitoreo_id',
        'user_id',
        'nombre_completo',
        'cargo',
        'institucion'
    ];

    public function monitoreo()
    {
        return $this->belongsTo(Monitoreo::class);
    }
}