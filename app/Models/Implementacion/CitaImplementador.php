<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class CitaImplementador extends Model
{
    protected $table = 'citas_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(CitaActa::class, 'acta_id');
    }
}
