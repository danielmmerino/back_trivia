<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
public function login(Request $request)
{
    try {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Buscar por el campo correcto
        $user = \App\Models\User::where('correo_usuario', $credentials['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Comparar correctamente con Hash
        if (! \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->clave)) {
            return response()->json(['message' => 'Contraseña incorrecta'], 401);
        }

        // Generar token base (puedes mejorar luego con JWT)
        $hash = hash_hmac('sha256', $user->correo_usuario, env('API_KEY', 'default'));
        $token = base64_encode($user->uuid . '|' . $hash);

        return response()->json(['token' => $token]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function loginUsuarios(Request $request)
    {
        $credentials = $request->validate([
            'correo_usuario' => ['required', 'email'],
            'clave' => ['required'],
        ]);

        $query = 'select uuid, nombre_usario, correo_usuario, estado_usuario, clave, fecha_creacion, fecha_actualizacion '
            . 'from usuarios_usuario where correo_usuario = ? and clave = ?';

        $result = DB::connection('usuarios')->select($query, [
            $credentials['correo_usuario'],
            $credentials['clave'],
        ]);

        if (empty($result)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json((array) $result[0]);
    }
}
