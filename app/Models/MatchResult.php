<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchResult extends Model
{
    protected $table = 'partida_resultados';
    public $timestamps = false;

    protected $fillable = [
        'idPartida',
        'respuestas_correctas',
        'respuestas_incorrectas',
        'nickname',
        'fecha_creacion',
    ];
}
