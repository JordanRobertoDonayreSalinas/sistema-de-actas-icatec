<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    protected $table = 'reuniones';

    protected $fillable = [
        'titulo_reunion',
        'fecha_reunion',
        'hora_reunion',
        'hora_finalizada_reunion',
        'nombre_institucion',
        'descripcion_general',
        'acuerdos',
        'comentarios_observaciones',
        'foto_1',
        'foto_2',
        'participantes',
        'anulado'
    ];

    protected $casts = [
        'acuerdos' => 'array',
        'comentarios_observaciones' => 'array',
        'participantes' => 'array',
        'anulado' => 'boolean'
    ];
}
