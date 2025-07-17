<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchQuestion extends Model
{
    protected $table = 'partida_preguntas';
    public $timestamps = false;

    protected $fillable = [
        'id_sesion',
        'id_pregunta',
        'esCorrecto',
        'fecha_creacion',
    ];
}
