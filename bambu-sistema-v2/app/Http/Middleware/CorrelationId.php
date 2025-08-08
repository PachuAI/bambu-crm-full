<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID') ?? (string) Str::uuid();

        // Agregar al contexto de logs
        Log::withContext([
            'requestId' => $requestId,
            'userId' => optional(auth()->user())->id,
            'ip' => $request->ip(),
            'userAgent' => $request->userAgent(),
        ]);

        $response = $next($request);

        // Agregar correlation ID a todas las respuestas
        if ($response instanceof Response) {
            $response->headers->set('X-Request-ID', $requestId);
        }

        return $response;
    }
}
