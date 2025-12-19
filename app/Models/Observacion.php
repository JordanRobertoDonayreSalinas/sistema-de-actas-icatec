<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    use HasFactory;

    // 👇 Forzamos el nombre correcto de la tabla
    protected $table = 'observaciones';

    protected $fillable = [
        'acta_id',
        'descripcion',
    ];

    public function acta()
    {
        return $this->belongsTo(Acta::class);
    }
}
