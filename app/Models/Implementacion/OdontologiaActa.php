<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class OdontologiaActa extends Model
{
    protected $table = 'odontologia_actas';
    
    
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
        return $this->hasMany(OdontologiaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(OdontologiaImplementador::class, 'acta_id');
    }
}

