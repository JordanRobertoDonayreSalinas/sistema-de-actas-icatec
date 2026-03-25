<?php

namespace App\Models\Implementacion;

use Illuminate\Database\Eloquent\Model;

class FuaImplementador extends Model
{
    protected $table = 'fua_implem_actas';
    
    protected $fillable = [
        'acta_id', 'dni', 'apellido_paterno', 'apellido_materno', 
        'nombres', 'cargo'
    ];

    public function acta()
    {
        return $this->belongsTo(FuaActa::class, 'acta_id');
    }
}
