<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Profesional;
use App\Models\CabeceraMonitoreo;

class ComDificultad extends Model
{
    use HasFactory;

    protected $table = 'com_dificultad';

    protected $fillable = [
        'acta_id',
        'modulo_id',
        'profesional_id',
        'insti_comunica', 
        'medio_comunica', 
        'comentarios'
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
