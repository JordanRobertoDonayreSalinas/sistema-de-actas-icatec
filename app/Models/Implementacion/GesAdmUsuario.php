<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class GesAdmUsuario extends Model
{
    protected $table = 'ges_adm_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(GesAdmActa::class, 'acta_id');
    }
}
