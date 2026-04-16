<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramacionSectorPropuesta extends Model
{
    use HasFactory;

    protected $table = 'programacion_sectores_propuesta';

    protected $fillable = [
        'establecimiento_id',
        'nombre_pdf',
        'provincia',
        'sector',
        'cuadril',
        'comienzo',
        'fin',
        'dias'
    ];

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }
}
