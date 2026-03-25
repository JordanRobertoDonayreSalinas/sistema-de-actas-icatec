<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class ReferenciaImplementador extends Model
{
    protected $table = 'referencias_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(ReferenciaActa::class, 'acta_id');
    }
}
