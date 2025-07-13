<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $user = User::where('correo_usuario', $credentials['email'])->first();

            if (! $user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            if (! Hash::check($credentials['password'], $user->clave)) {
                return response()->json(['message' => 'Contraseña incorrecta'], 401);
            }

            $payload = [
                'sub' => $user->uuid,
                'iat' => time(),
                'exp' => time() + 3600,
            ];
            $token = JWT::encode($payload, env('APP_KEY'), 'HS256');

            UserSession::create([
                'uuid_usuario' => $user->uuid,
                'token' => $token,
                'fecha_creacion' => Carbon::now(),
                'estado' => 1,
            ]);

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

    public function socialLogin(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => ['required', 'email'],
            ]);

            $user = User::where('correo_usuario', $data['email'])->first();

            if (! $user) {
                $user = new User();
                $user->uuid = (string) Str::uuid();
                $user->nombre_usario = $data['email'];
                $user->correo_usuario = $data['email'];
                $user->clave = '';
                $user->estado_usuario = 1;
                $user->fecha_creacion = Carbon::now();
                $user->fecha_actualizacion = Carbon::now();
                $user->save();
            }

            $payload = [
                'sub' => $user->uuid,
                'iat' => time(),
                'exp' => time() + 3600,
            ];
            $token = JWT::encode($payload, env('APP_KEY'), 'HS256');

            UserSession::create([
                'uuid_usuario' => $user->uuid,
                'token' => $token,
                'fecha_creacion' => Carbon::now(),
                'estado' => 1,
            ]);

            return response()->json(['token' => $token]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        try {
            $payload = JWT::decode($token, new Key(env('APP_KEY'), 'HS256'));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token expired', 'requires_new_token' => true], 401);
        }

        $session = UserSession::where('token', $token)->where('estado', 1)->first();
        if (! $session) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        $user = User::find($payload->sub);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        $session->estado = 0;
        $session->save();

        $payload = [
            'sub' => $user->uuid,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $newToken = JWT::encode($payload, env('APP_KEY'), 'HS256');

        UserSession::create([
            'uuid_usuario' => $user->uuid,
            'token' => $newToken,
            'fecha_creacion' => Carbon::now(),
            'estado' => 1,
        ]);

        return response()->json(['token' => $newToken]);
    }
}
