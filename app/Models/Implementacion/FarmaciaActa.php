<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class FarmaciaActa extends Model
{
    protected $table = 'farmacia_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(FarmaciaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(FarmaciaImplementador::class, 'acta_id');
    }
}

