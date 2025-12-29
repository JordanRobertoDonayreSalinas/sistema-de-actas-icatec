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
     * Se incluye 'id' si fuera necesario, aunque Laravel lo maneja automáticamente.
     */
    protected $fillable = [
        'tipo_doc',
        'doc',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'email',
        'telefono',
    ];

    /**
     * Al haber agregado el campo 'id' como autoincrementable y primaria,
     * ya no necesitas configurar $incrementing ni $primaryKey, 
     * ya que Laravel asume estos valores por defecto.
     */

    /**
     * Indica si el modelo debe tener marcas de tiempo (created_at, updated_at).
     */
    public $timestamps = true;

    /**
     * Mutador: Asegura que el DNI/Documento siempre se guarde sin espacios.
     */
    public function setDocAttribute($value)
    {
        $this->attributes['doc'] = trim($value);
    }

    /**
     * Mutadores: Convierten nombres y apellidos a mayúsculas antes de guardar.
     */
    public function setApellidoPaternoAttribute($value)
    {
        $this->attributes['apellido_paterno'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    public function setApellidoMaternoAttribute($value)
    {
        $this->attributes['apellido_materno'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = mb_strtoupper(trim($value), 'UTF-8');
    }

    /**
     * Mutador: Asegura que el email se guarde siempre en minúsculas.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }
}