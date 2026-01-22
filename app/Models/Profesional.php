<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'mon_profesionales';

    /**
     * Los atributos que se pueden asignar masivamente.
     * Al usar updateOrCreate en el controlador, todos estos deben estar aquí.
     */
    protected $fillable = [
        'tipo_doc',
        'doc',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'email',
        'cargo',
        'telefono',
    ];

    /**
     * Indica si el modelo debe tener marcas de tiempo (created_at, updated_at).
     * Ahora que ejecutaste la migración, esto DEBE ser true.
     */
    public $timestamps = true;

    // =========================================================================
    // MUTADORES (Setters)
    // Estos aseguran que los datos se limpien automáticamente antes de entrar a la DB
    // =========================================================================

    /**
     * Asegura que el DNI/Documento siempre se guarde sin espacios en los extremos.
     */
    public function setDocAttribute($value)
    {
        $this->attributes['doc'] = trim($value);
    }

    /**
     * Convierte el Apellido Paterno a mayúsculas y limpia espacios.
     */
    public function setApellidoPaternoAttribute($value)
    {
        $this->attributes['apellido_paterno'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    /**
     * Convierte el Apellido Materno a mayúsculas y limpia espacios.
     */
    public function setApellidoMaternoAttribute($value)
    {
        $this->attributes['apellido_materno'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    /**
     * Convierte los Nombres a mayúsculas y limpia espacios.
     */
    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    /**
     * Asegura que el email se guarde siempre en minúsculas para evitar duplicados por formato.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = !empty($value) ? strtolower(trim($value)) : null;
    }
}