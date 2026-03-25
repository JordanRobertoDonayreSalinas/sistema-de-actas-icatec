<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class TriajeUsuario extends Model
{
    protected $table = 'triaje_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(TriajeActa::class, 'acta_id');
    }
}
