<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAdministrativo extends Model
{
    protected $table = 'documentos_administrativos';

    protected $fillable = [
        'fecha', 'establecimiento_id', 'profesional_tipo_doc', 'profesional_doc',
        'profesional_nombre', 'profesional_apellido_paterno', 'profesional_apellido_materno',
        'cargo_rol', 'area_oficina', 'sistemas_acceso', 'correo_electronico',
        'tipo_formato', 'pdf_generado_path', 'pdf_firmado_path', 'user_id'
    ];

    public function establecimiento() {
        return $this->belongsTo(Establecimiento::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}