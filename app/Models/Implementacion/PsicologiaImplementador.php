<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class PsicologiaImplementador extends Model
{
    protected $table = 'psicologia_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(PsicologiaActa::class, 'acta_id');
    }
}
