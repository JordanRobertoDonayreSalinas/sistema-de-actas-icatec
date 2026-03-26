<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class MedicinaActa extends Model
{
    protected $table = 'medicina_actas';
    
    protected $fillable = [
        'firma_digital', 'modulo', 'fecha', 'codigo_establecimiento', 'nombre_establecimiento',
        'provincia', 'distrito', 'categoria', 'red', 'microred',
        'responsable', 'modalidad', 'observaciones', 'foto1', 'foto2'
    ];

    public function usuarios()
    {
        return $this->hasMany(MedicinaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(MedicinaImplementador::class, 'acta_id');
    }
}

