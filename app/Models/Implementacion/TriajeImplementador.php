<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class TriajeImplementador extends Model
{
    protected $table = 'triaje_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(TriajeActa::class, 'acta_id');
    }
}
