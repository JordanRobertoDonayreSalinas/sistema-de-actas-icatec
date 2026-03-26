<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class LaboratorioActa extends Model
{
    protected $table = 'laboratorio_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(LaboratorioUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(LaboratorioImplementador::class, 'acta_id');
    }
}

