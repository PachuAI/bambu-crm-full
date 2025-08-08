<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Idempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar idempotencia a métodos de escritura
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key');

        // Si no hay key, requerir para operaciones críticas (excepto en testing)
        if (! $idempotencyKey) {
            if (! app()->environment('testing')) {
                abort(400, 'Header Idempotency-Key es requerido para esta operación');
            }
            // Para testing, generar key automática
            $idempotencyKey = (string) Str::uuid();
        }

        // Generar hash único por usuario, ruta, método y key
        $hash = 'idem:'.sha1(
            $request->path().
            $request->method().
            $idempotencyKey.
            (auth()->id() ?? 'anonymous')
        );

        // Verificar si ya existe una respuesta cacheada
        $cached = Cache::get($hash);
        if ($cached) {
            Log::info('Idempotent request served from cache', [
                'key' => $idempotencyKey,
                'hash' => $hash,
                'user_id' => auth()->id(),
            ]);

            return response()->json($cached['body'], $cached['status'])
                ->header('X-Idempotent-Replay', 'true')
                ->header('X-Request-ID', request()->header('X-Request-ID'));
        }

        // Procesar request normalmente
        $response = $next($request);

        // Solo cachear respuestas exitosas
        if ($response->status() >= 200 && $response->status() < 300) {
            Cache::put($hash, [
                'body' => $response->getOriginalContent(),
                'status' => $response->status(),
            ], now()->addMinutes(10)); // Cache por 10 minutos

            Log::info('Response cached for idempotency', [
                'key' => $idempotencyKey,
                'hash' => $hash,
                'status' => $response->status(),
            ]);
        }

        return $response;
    }
}
