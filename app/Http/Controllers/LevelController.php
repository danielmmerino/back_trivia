<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LevelController extends Controller
{
    public function index()
    {
        $levels = DB::table('juego_dificultad')
            ->select('id', 'nombre')
            ->where('estado', 1)
            ->get();

        return response()->json($levels);
    }
}
