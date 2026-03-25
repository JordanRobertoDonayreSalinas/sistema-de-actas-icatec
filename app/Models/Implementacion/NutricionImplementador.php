<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class NutricionImplementador extends Model
{
    protected $table = 'nutricion_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(NutricionActa::class, 'acta_id');
    }
}
