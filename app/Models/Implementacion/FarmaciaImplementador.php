<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class FarmaciaImplementador extends Model
{
    protected $table = 'farmacia_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(FarmaciaActa::class, 'acta_id');
    }
}
