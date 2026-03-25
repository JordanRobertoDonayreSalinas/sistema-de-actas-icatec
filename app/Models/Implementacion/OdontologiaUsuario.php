<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class OdontologiaUsuario extends Model
{
    protected $table = 'odontologia_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(OdontologiaActa::class, 'acta_id');
    }
}
