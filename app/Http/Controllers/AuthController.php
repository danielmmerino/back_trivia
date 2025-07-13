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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $hash = hash_hmac('sha256', $user->email, env('API_KEY'));
        $token = base64_encode($user->id.'|'.$hash);

        return response()->json(['token' => $token]);
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
