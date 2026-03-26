<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class MentalActa extends Model
{
    protected $table = 'mental_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2'
    ];

    public function usuarios()
    {
        return $this->hasMany(MentalUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(MentalImplementador::class, 'acta_id');
    }
}

