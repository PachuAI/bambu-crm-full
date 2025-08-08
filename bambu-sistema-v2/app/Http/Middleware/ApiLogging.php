<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLogging
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        // Log a canal específico de accesos API
        Log::channel('api_access')->info('API Request', [
            'timestamp' => now()->toISOString(),
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_id' => $request->header('X-Request-ID'),
            'content_length' => strlen($response->getContent()),
            'route' => optional($request->route())->getName(),
        ]);
        
        // Log errores con más detalle
        if ($response->getStatusCode() >= 400) {
            Log::channel('json')->error('API Error Response', [
                'timestamp' => now()->toISOString(),
                'method' => $request->method(),
                'uri' => $request->getRequestUri(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'request_id' => $request->header('X-Request-ID'),
                'request_data' => $request->method() !== 'GET' ? $request->except(['password', 'password_confirmation']) : null,
                'response_data' => $response->getStatusCode() < 500 ? json_decode($response->getContent(), true) : null,
            ]);
        }
        
        return $response;
    }
}