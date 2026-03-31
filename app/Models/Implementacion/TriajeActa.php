<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class TriajeActa extends Model
{
    protected $table = 'triaje_actas';
    
    protected $fillable = [
        'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2', 'archivo_pdf'
    ];

    public function usuarios()
    {
        return $this->hasMany(TriajeUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(TriajeImplementador::class, 'acta_id');
    }
}
