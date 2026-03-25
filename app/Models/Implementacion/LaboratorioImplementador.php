<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class LaboratorioImplementador extends Model
{
    protected $table = 'laboratorio_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(LaboratorioActa::class, 'acta_id');
    }
}
