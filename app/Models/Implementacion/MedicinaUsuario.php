<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class MedicinaUsuario extends Model
{
    protected $table = 'medicina_usu_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'celular', 'correo', 'permisos'
    ];

    public function acta()
    {
        return $this->belongsTo(MedicinaActa::class, 'acta_id');
    }
}
