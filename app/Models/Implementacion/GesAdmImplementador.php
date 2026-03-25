<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class GesAdmImplementador extends Model
{
    protected $table = 'ges_adm_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(GesAdmActa::class, 'acta_id');
    }
}
