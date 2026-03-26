<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class PsicologiaActa extends Model
{
    protected $table = 'psicologia_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(PsicologiaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(PsicologiaImplementador::class, 'acta_id');
    }
}

