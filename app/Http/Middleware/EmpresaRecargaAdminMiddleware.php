<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EmpresaRecargaAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        // Validar autenticación, rol admin (1) y NIT de empresa de recarga
        if (!$user || $user->id_tipo_usuario != 10) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }
        return $next($request);
    }
}
