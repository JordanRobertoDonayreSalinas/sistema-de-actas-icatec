<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class FuaUsuario extends Model
{
    protected $table = 'fua_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(FuaActa::class, 'acta_id');
    }
}
