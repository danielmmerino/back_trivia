<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\MatchResult;
use App\Models\UserSession;

class ResultController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'preguntasCorrectas' => 'required|integer',
                'preguntasIncorrectas' => 'required|integer',
                'nickname' => 'required|string|max:200',
            ]);

            $token = $request->bearerToken();
            $session = UserSession::where('token', $token)->where('estado', 1)->first();

            if (! $session) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            MatchResult::create([
                'idPartida' => $session->id,
                'respuestas_correctas' => $data['preguntasCorrectas'],
                'respuestas_incorrectas' => $data['preguntasIncorrectas'],
                'nickname' => $data['nickname'],
                'fecha_creacion' => now(),
            ]);

            return response()->json(['message' => 'Resultados almacenados'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
