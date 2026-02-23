<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalApiKey
{
    // =========================================================
    // VALIDAR API KEY PARA ROTAS INTERNAS
    // HEADER: X-INTERNAL-KEY
    // ENV: INTERNAL_API_KEY
    // =========================================================
    public function handle(Request $request, Closure $next)
    {
        $expected = env('INTERNAL_API_KEY');
        $provided = $request->header('X-INTERNAL-KEY');

        // =========================================================
        // SE NÃO CONFIGURADO OU NÃO ENVIADO, BLOQUEIA
        // =========================================================
        if (!$expected || !$provided || !hash_equals($expected, $provided)) {
            return response()->json(['error' => 'NÃO AUTORIZADO'], 401);
        }

        return $next($request);
    }
}