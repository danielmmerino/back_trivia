<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class TokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        try {
            $payload = JWT::decode($token, new Key(env('APP_KEY'), 'HS256'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['message' => 'Token expired', 'requires_new_token' => true], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        $session = UserSession::where('token', $token)->where('estado', 1)->first();
        if (! $session) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        if (Carbon::parse($session->fecha_creacion)->addHour()->isPast()) {
            $session->estado = 0;
            $session->save();
            return response()->json(['message' => 'Token expired', 'requires_new_token' => true], 401);
        }

        $user = User::find($payload->sub);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated', 'requires_new_token' => true], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
