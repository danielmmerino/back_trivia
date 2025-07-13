<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'id_categoria' => 'required|integer',
            'limit' => 'required|integer',
            'id_dificultad' => 'required|integer',
        ]);

        $preguntas = DB::select(
            'select id, categoria, descripcion, id_dificultad, estado, fecha_creacion
            from juego_preguntas where categoria = ? and estado = 1 and id_dificultad = ?
            order by id_dificultad limit ?;',
            [
                $data['id_categoria'],
                $data['id_dificultad'],
                $data['limit'],
            ]
        );

        $resultado = [];
        foreach ($preguntas as $pregunta) {
            $opciones = DB::select(
                'select id, id_pregunta, descripcion, esCorrecto, estado
                from juego_opciones where id_pregunta = ? and estado = 1 limit 4;',
                [$pregunta->id]
            );

            $opcionesFormato = [];
            foreach ($opciones as $index => $opcion) {
                $opcionesFormato[] = [
                    'orden' => (string) ($index + 1),
                    'opcion' => $opcion->descripcion,
                    'esCorrecta' => $opcion->esCorrecto ? 'true' : 'false',
                ];
            }

            $resultado[] = [
                'pregunta' => $pregunta->descripcion,
                'id_categoria' => $pregunta->categoria,
                'id_dificultad' => $pregunta->id_dificultad,
                'opciones' => $opcionesFormato,
            ];
        }

        return response()->json($resultado);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pregunta' => 'required|string',
            'id_categoria' => 'required|integer',
            'id_dificultad' => 'required|integer',
            'opciones' => 'required|array|size:4',
            'opciones.*.opcion' => 'required|string',
            'opciones.*.esCorrecta' => 'required|in:true,false',
        ]);

        $hasCorrect = collect($data['opciones'])->contains(function ($op) {
            return $op['esCorrecta'] === 'true';
        });

        if (! $hasCorrect) {
            return response()->json(['message' => 'Debe existir al menos una respuesta correcta'], 400);
        }

        $preguntaId = DB::table('juego_preguntas')->insertGetId([
            'categoria' => $data['id_categoria'],
            'descripcion' => $data['pregunta'],
            'id_dificultad' => $data['id_dificultad'],
            'estado' => 1,
            'fecha_creacion' => now(),
        ]);

        foreach ($data['opciones'] as $opcion) {
            DB::table('juego_opciones')->insert([
                'id_pregunta' => $preguntaId,
                'descripcion' => $opcion['opcion'],
                'esCorrecto' => $opcion['esCorrecta'] === 'true',
                'estado' => 1,
                'fecha_creacion' => now(),
            ]);
        }

        return response()->json(['message' => 'Pregunta creada'], 201);
    }
}
