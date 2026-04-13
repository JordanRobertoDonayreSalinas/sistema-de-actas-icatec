<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class LaboratorioActa extends Model
{
    protected $table = 'laboratorio_actas';
    
    
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
        return $this->hasMany(LaboratorioUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(LaboratorioImplementador::class, 'acta_id');
    }
}

