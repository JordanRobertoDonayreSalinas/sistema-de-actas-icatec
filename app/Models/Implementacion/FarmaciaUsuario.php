<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class FarmaciaUsuario extends Model
{
    protected $table = 'farmacia_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(FarmaciaActa::class, 'acta_id');
    }
}
