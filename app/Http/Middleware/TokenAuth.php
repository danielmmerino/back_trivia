<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $decoded = base64_decode(substr($header, 7));
        if (! $decoded || ! str_contains($decoded, '|')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        [$id, $hash] = explode('|', $decoded, 2);
        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $expected = hash_hmac('sha256', $user->email, env('API_KEY'));
        if (! hash_equals($expected, $hash)) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
