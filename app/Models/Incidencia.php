<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incidencia extends Model
{
    protected $table = 'incidencias';

    protected $fillable = [
        'dni',
        'apellidos',
        'nombres',
        'celular',
        'correo',
        'codigo_ipress',
        'nombre_establecimiento',
        'distrito_establecimiento',
        'provincia_establecimiento',
        'categoria',
        'red',
        'microred',
        'jefe_establecimiento',
        'modulos',
        'observacion',
        'imagen1',
        'imagen2',
        'imagen3',
        'estado',
    ];

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaIncidencia::class);
    }
}
