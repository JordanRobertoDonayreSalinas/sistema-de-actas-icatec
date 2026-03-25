<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class MedicinaImplementador extends Model
{
    protected $table = 'medicina_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(MedicinaActa::class, 'acta_id');
    }
}
