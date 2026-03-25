<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class ReferenciaUsuario extends Model
{
    protected $table = 'referencias_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(ReferenciaActa::class, 'acta_id');
    }
}
