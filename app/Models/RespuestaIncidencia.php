<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaIncidencia extends Model
{
    protected $table = 'respuestas_incidencias';

    protected $fillable = [
        'incidencia_id',
        'user_id',
        'respuesta',
        'estado',
        'imagen1',
        'imagen2',
        'imagen3',
    ];

    public function incidencia(): BelongsTo
    {
        return $this->belongsTo(Incidencia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
