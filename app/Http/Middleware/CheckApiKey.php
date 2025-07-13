<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('x-api-key');
        if (! $key || $key !== env('API_KEY')) {
            return response()->json(['message' => 'Invalid API key'], 401);
        }

        return $next($request);
    }
}
