<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\UserSession;
use App\Models\MatchQuestion;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        try {
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

            $token = $request->bearerToken();
            $session = UserSession::where('token', $token)->where('estado', 1)->first();
            if ($session) {
                foreach ($preguntas as $preg) {
                    MatchQuestion::create([
                        'id_sesion' => $session->id,
                        'id_pregunta' => $preg->id,
                        'esCorrecto' => null,
                        'fecha_creacion' => now(),
                    ]);
                }
            }

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
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $questions = $request->all();

            // Allow sending a single object instead of an array
            if (isset($questions['pregunta'])) {
                $questions = [$questions];
            }

            $validated = validator(['questions' => $questions], [
                'questions' => 'required|array',
                'questions.*.pregunta' => 'required|string|max:255',
                'questions.*.id_categoria' => 'required|integer',
                'questions.*.id_dificultad' => 'required|integer',
                'questions.*.opciones' => 'required|array|size:4',
                'questions.*.opciones.*.opcion' => 'required|string|max:255',
                'questions.*.opciones.*.esCorrecta' => 'required|in:true,false',
            ])->validate()['questions'];

            foreach ($validated as $question) {
                $hasCorrect = collect($question['opciones'])->contains(function ($op) {
                    return $op['esCorrecta'] === 'true';
                });

                if (! $hasCorrect) {
                    return response()->json(['message' => 'Debe existir al menos una respuesta correcta'], 400);
                }

                $preguntaId = DB::table('juego_preguntas')->insertGetId([
                    'categoria' => $question['id_categoria'],
                    'descripcion' => $question['pregunta'],
                    'id_dificultad' => $question['id_dificultad'],
                    'estado' => 1,
                    'fecha_creacion' => now(),
                ]);

                foreach ($question['opciones'] as $opcion) {
                    DB::table('juego_opciones')->insert([
                        'id_pregunta' => $preguntaId,
                        'descripcion' => $opcion['opcion'],
                        'esCorrecto' => $opcion['esCorrecta'] === 'true',
                        'estado' => 1,
                        'fecha_creacion' => now(),
                    ]);
                }
            }

            return response()->json(['message' => 'Preguntas creadas'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
