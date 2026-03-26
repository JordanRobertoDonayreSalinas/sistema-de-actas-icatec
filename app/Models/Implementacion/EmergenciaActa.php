<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class EmergenciaActa extends Model
{
    protected $table = 'emergencia_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(EmergenciaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(EmergenciaImplementador::class, 'acta_id');
    }
}

