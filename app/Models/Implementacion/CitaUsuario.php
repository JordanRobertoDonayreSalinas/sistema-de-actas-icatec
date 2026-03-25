<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class CitaUsuario extends Model
{
    protected $table = 'citas_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(CitaActa::class, 'acta_id');
    }
}
