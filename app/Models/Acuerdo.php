<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acuerdo extends Model
{
    use HasFactory;

    protected $fillable = [
        'acta_id',
        'descripcion',
    ];

    public function acta()
    {
        return $this->belongsTo(Acta::class);
    }
}
