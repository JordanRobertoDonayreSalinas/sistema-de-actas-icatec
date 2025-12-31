<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Profesional;
use App\Models\CabeceraMonitoreo;

class ComEquipamiento extends Model
{
    use HasFactory;

    protected $table = 'com_equipamiento';

    protected $fillable = [
        'acta_id',
        'modulo_id',
        'profesional_id',
        'descripcion',
        'cantidad',      // Aquí guardaremos el CÓDIGO/SERIE según tu vista
        'propiedad',
        'estado',
        'cod_barras',
        'observaciones',
        'comentarios',   // Aquí guardaremos el comentario general
    ];

    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id');
    }
    
    public function acta()
    {
        return $this->belongsTo(CabeceraMonitoreo::class, 'acta_id');
    }
}
