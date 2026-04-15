<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class PsicologiaActa extends Model
{
    protected $table = 'psicologia_actas';
    
    
    protected $casts = [
        'renipress_data' => 'array'
    ];

protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2', 'archivo_pdf', 'anulado', 'renipress_data'
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

