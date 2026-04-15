<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramacionSector extends Model
{
    protected $table = 'programacion_sectores';

    protected $fillable = [
        'establecimiento_id',
        'nombre_pdf',
        'provincia',
        'sector',
        'cuadril',
        'comienzo',
        'fin',
        'dias',
    ];

    protected $casts = [
        'comienzo' => 'date',
        'fin'      => 'date',
    ];

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
}
