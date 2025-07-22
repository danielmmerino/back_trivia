<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('x-api-key');
        $expectedKey = env('API_KEY');
        //$expectedKey= config('api.key');

        // Para debug temporal (puedes comentar luego)
        // Log::info('API Key Provided: ' . $providedKey);
        // Log::info('API Key Expected: ' . $expectedKey);

        // Verificación
        if (!$providedKey || $providedKey !== $expectedKey) {
            return response()->json([
                'message' => 'Invalid API key',
                'provided' => $providedKey,
                //'expected' => $expectedKey, // ⚠️ Quita esto en producción
            ], 401);
        }

        return $next($request);
    }
}
