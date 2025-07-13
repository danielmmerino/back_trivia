<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $table = 'usuarios_sesiones';
    public $timestamps = false;
    protected $fillable = [
        'uuid_usuario',
        'token',
        'fecha_creacion',
        'estado',
    ];
}
