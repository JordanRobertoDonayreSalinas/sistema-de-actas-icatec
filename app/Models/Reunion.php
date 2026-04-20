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

    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value, $flags = 0)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | $flags);
    }
}
