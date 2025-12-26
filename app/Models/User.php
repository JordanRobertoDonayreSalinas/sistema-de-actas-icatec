<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos habilitados para asignación masiva.
     * Se han incluido los campos de apellidos y el estado (status).
     */
    protected $fillable = [
        'name',             // Representa los "Nombres"
        'apellido_paterno', // Nuevo
        'apellido_materno', // Nuevo
        'email',
        'username',         // DNI
        'password',
        'role',
        'status',           // Nuevo (para activo/inactivo)
    ];

    /**
     * Atributos ocultos para la serialización (por ejemplo, al convertir a JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casteo de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Opcional: Accessor para obtener el nombre completo fácilmente.
     * Uso: $user->full_name
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}