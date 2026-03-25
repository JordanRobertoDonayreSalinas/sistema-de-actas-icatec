<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class LaboratorioUsuario extends Model
{
    protected $table = 'laboratorio_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(LaboratorioActa::class, 'acta_id');
    }
}
