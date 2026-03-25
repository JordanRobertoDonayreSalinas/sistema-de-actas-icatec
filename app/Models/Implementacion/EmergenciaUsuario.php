<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class EmergenciaUsuario extends Model
{
    protected $table = 'emergencia_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(EmergenciaActa::class, 'acta_id');
    }
}
