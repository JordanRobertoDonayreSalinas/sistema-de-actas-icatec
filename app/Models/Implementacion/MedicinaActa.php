<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class MedicinaActa extends Model
{
    protected $table = 'medicina_actas';
    
    
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
        return $this->hasMany(MedicinaUsuario::class, 'acta_id');
    }

    public function implementadores()
    {
        return $this->hasMany(MedicinaImplementador::class, 'acta_id');
    }
}

