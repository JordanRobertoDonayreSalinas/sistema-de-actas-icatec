<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class OdontologiaActa extends Model
{
    protected $table = 'odontologia_actas';
    
    protected $fillable = [
        'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(OdontologiaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(OdontologiaImplementador::class, 'acta_id');
    }
}
