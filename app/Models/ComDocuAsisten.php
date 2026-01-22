<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Profesional;
use App\Models\CabeceraMonitoreo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComDocuAsisten extends Model
{
    use HasFactory;

    protected $table = 'com_docu_asisten';

    protected $fillable = [
        'acta_id',
        'modulo_id',
        'profesional_id',
        'cant_consultorios',
        'nombre_consultorio',
        'turno',
        'fecha_registro',
        'fua',
        'referencia',
        'receta',
        'orden_laboratorio',
        'comentarios',
    ];

    public function acta()
    {
        return $this->belongsTo(CabeceraMonitoreo::class, 'acta_id');
    }

    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id');
    }
}
