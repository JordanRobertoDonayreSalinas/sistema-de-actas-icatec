<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    protected $table = 'establecimientos';

    protected $fillable = [
        'codigo',
        'nombre',
        'distrito',
        'provincia',
        'microred',
        'red',
        'responsable'
    ];

    public $timestamps = false;

    /**
     * ========================================================================
     * RELACIONES (Necesaria para el Dashboard)
     * ========================================================================
     * Un establecimiento puede tener muchas actas asociadas.
     */
    public function actas()
    {
        // Asume que en la tabla 'actas' existe la columna 'establecimiento_id'
        return $this->hasMany(Acta::class, 'establecimiento_id');
    }
}