<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class NutricionUsuario extends Model
{
    protected $table = 'nutricion_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(NutricionActa::class, 'acta_id');
    }
}
