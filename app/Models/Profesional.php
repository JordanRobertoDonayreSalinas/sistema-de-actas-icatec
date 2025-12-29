<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     * * @var string
     */
    protected $table = 'mon_profesionales';

    /**
     * Los atributos que se pueden asignar masivamente.
     * Estos coinciden con los campos que envías desde el formulario.
     * * @var array
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
     * Desactiva el incremento si el 'doc' fuera la llave primaria manual,
     * pero si usas un ID autoincrementable común, puedes omitir estas líneas.
     */
    // public $incrementing = true;
    // protected $primaryKey = 'id';

    /**
     * Indica si el modelo debe tener marcas de tiempo (created_at, updated_at).
     * * @var bool
     */
    public $timestamps = true;

    /**
     * Mutador opcional: Asegura que el DNI/Documento siempre se guarde sin espacios
     */
    public function setDocAttribute($value)
    {
        $this->attributes['doc'] = trim($value);
    }

    /**
     * Mutador opcional: Convierte nombres y apellidos a mayúsculas antes de guardar
     */
    public function setApellidoPaternoAttribute($value)
    {
        $this->attributes['apellido_paterno'] = strtoupper($value);
    }

    public function setApellidoMaternoAttribute($value)
    {
        $this->attributes['apellido_materno'] = strtoupper($value);
    }

    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = strtoupper($value);
    }
}