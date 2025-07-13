<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ✅ Usar tabla personalizada
    protected $table = 'usuarios_usuario';

    // ✅ Usar clave primaria personalizada
    protected $primaryKey = 'uuid';

    // ✅ Si no es un integer autoincremental
    public $incrementing = false;

    // ✅ Si `uuid` es string/char
    protected $keyType = 'string';

      public $timestamps = false; // ✅ Desactiva las columnas created_at y updated_at

    // ✅ Campos que se pueden asignar en masa
    protected $fillable = [
        'nombre_usario',
        'correo_usuario',
        'clave',
        'estado_usuario',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    // ✅ Campos ocultos en respuestas JSON
    protected $hidden = [
        'clave',
    ];

    // ✅ Casts si los necesitas
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    // ✅ Personalizar el campo de contraseña que usa Laravel
    public function getAuthPassword()
    {
        return $this->clave;
    }

    // ✅ Si quieres que Laravel use `correo_usuario` como identificador
    public function getAuthIdentifierName()
    {
        return 'correo_usuario';
    }
}
