<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class MentalUsuario extends Model
{
    protected $table = 'mental_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(MentalActa::class, 'acta_id');
    }
}
