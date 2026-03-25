<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class GesAdmActa extends Model
{
    protected $table = 'ges_adm_actas';
    
    protected $fillable = [
        'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones'
    ];

    public function usuarios()
    {
        return $this->hasMany(GesAdmUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(GesAdmImplementador::class, 'acta_id');
    }
}
