<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class ReferenciaActa extends Model
{
    protected $table = 'referencias_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(ReferenciaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(ReferenciaImplementador::class, 'acta_id');
    }
}

