<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== config('services.n8n.key')) {
            // 3. Si la clé est mauvaise ou absente, on renvoie une erreur 401
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        return $next($request);
    }
}
