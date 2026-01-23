<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CabeceraMonitoreo;

class ComentariosEsp extends Model
{
    protected $table = 'comp_comentarios_esp';

    protected $fillable = [
        'acta_id',
        'comentario_esp',
        'foto_url_esp',
    ];

    public function acta() {
        return $this->belongsTo(CabeceraMonitoreo::class, 'acta_id');
    }
}
