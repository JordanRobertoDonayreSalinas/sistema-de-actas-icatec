<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class FuaActa extends Model
{
    protected $table = 'fua_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2', 'archivo_pdf'
    ];

    public function usuarios()
    {
        return $this->hasMany(FuaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(FuaImplementador::class, 'acta_id');
    }
}

