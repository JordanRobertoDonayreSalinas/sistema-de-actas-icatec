<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class OdontologiaImplementador extends Model
{
    protected $table = 'odontologia_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(OdontologiaActa::class, 'acta_id');
    }
}
