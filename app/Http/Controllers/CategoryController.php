<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('juego_categoria')
            ->select('id', 'nombre', 'icono', 'estado')
            ->where('estado', 1)
            ->get();

        return response()->json($categories);
    }
}
