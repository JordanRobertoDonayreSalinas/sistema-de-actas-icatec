<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class GesAdmActa extends Model
{
    protected $table = 'ges_adm_actas';
    
    
    protected $casts = [
        'renipress_data' => 'array'
    ];

protected $fillable = [
        'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2', 'archivo_pdf', 'anulado', 'renipress_data'
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
