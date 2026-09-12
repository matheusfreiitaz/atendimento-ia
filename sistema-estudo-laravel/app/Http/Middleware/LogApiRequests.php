<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Este middleware roda em TODA rota do grupo "api".
     * Ele é o coração do exercício de MongoDB: cada chamada de API
     * vira um documento na coleção "api_logs".
     */
    public function handle(Request $request, Closure $next): Response
    {
        $inicio = microtime(true);

        $response = $next($request);

        $tempoMs = (microtime(true) - $inicio) * 1000;

        try {
            ApiLog::create([
                'metodo' => $request->method(),
                'rota' => optional($request->route())->getName() ?? $request->path(),
                'url_completa' => $request->fullUrl(),
                'parametros' => $request->all(),
                'status_code' => $response->getStatusCode(),
                'tempo_resposta_ms' => round($tempoMs, 2),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Se o Mongo cair, a API não pode quebrar por causa do log.
            report($e);
        }

        return $response;
    }
}
