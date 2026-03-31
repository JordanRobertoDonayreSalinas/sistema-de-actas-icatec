<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class CitaActa extends Model
{
    protected $table = 'citas_actas';
    
    protected $fillable = [
        'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2', 'archivo_pdf'
    ];

    public function usuarios()
    {
        return $this->hasMany(CitaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(CitaImplementador::class, 'acta_id');
    }
}
