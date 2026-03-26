<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class NutricionActa extends Model
{
    protected $table = 'nutricion_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2'
    ];

    public function usuarios()
    {
        return $this->hasMany(NutricionUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(NutricionImplementador::class, 'acta_id');
    }
}

