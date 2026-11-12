<?php

namespace App\Http\Middleware;

use App\Support\Permisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso en rutas: ->middleware('permiso:equipos.editar')
 * Con varios permisos separados por coma basta con tener uno: 'permiso:traslados.crear,bajas.crear'
 */
class VerificarPermiso
{
    public function handle(Request $request, Closure $next, string ...$permisos): Response
    {
        if (!Permisos::puedeAlguno($permisos)) {
            abort(403, 'Tu rol no tiene permiso para esta acción.');
        }

        return $next($request);
    }
}
