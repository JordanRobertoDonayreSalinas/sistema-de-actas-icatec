<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloParto extends Model
{
    use HasFactory;

    // 1. Nombre exacto de la tabla en tu base de datos
    protected $table = 'mon_modulo_parto';

    // 2. Columnas que permites guardar (Asignación masiva)
    protected $fillable = [
        'monitoreo_id',
        'nombre_consultorio',
        'personal_tipo_doc',
        'personal_dni',
        'personal_especialidad',
        'personal_nombre',
        'capacitacion_recibida',
        'capacitacion_entes',
        'capacitacion_otros_detalle',
        'insumos_disponibles',
        'materiales_otros',
        'equipos_listado',
        'equipos_observaciones',
        'nro_consultorios',
        'nro_gestantes_mes',
        'gestion_hisminsa',
        'gestion_reportes',
        'gestion_reportes_socializa',
        'dificultad_comunica_a',
        'dificultad_medio_uso',
        'fotos_evidencia',
        'firma_grafica'
    ];

    // 3. Conversión de JSON a Array y viceversa
    protected $casts = [
        'capacitacion_entes' => 'array',
        'insumos_disponibles' => 'array',
        'equipos_listado'     => 'array',
        'fotos_evidencia'     => 'array',
    ];
}
