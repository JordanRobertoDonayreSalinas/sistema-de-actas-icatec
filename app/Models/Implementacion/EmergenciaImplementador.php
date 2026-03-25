<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class EmergenciaImplementador extends Model
{
    protected $table = 'emergencia_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(EmergenciaActa::class, 'acta_id');
    }
}
