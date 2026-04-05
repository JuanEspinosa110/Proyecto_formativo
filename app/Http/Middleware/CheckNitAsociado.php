<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckNitAsociado
{
    /**
     * Maneja una solicitud entrante.
     * Verifica que los usuarios no-pasajeros y no-superadmin tengan un NIT.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si es SuperAdmin, dejar pasar (no usan NIT)
        if (Auth::guard('superadmin')->check()) {
            return $next($request);
        }

        // 2. Si no está autenticado bajo el guardia web, dejar pasar
        // (el middleware auth se encarga de la redirección al login si es necesario)
        if (!Auth::guard('web')->check()) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();

        // 3. Si el usuario es tipo Pasajero (ID 2), dejar pasar (no requieren NIT)
        if ((int) $user->id_tipo_usuario === 2) {
            return $next($request);
        }

        // 4. Si es un usuario administrativo/operativo y su NIT es nulo
        if (is_null($user->NIT)) {
            // Evitar bucle infinito si ya intenta ir a la página de error
            if ($request->routeIs('error.no-nit')) {
                return $next($request);
            }

            return redirect()->route('error.no-nit');
        }

        // 5. Verificar Licencia (Solo para Empresas de Transporte - ID 1)
        $empresa = \Illuminate\Support\Facades\DB::table('empresa')
            ->where('NIT', $user->NIT)
            ->first();

        if ($empresa && (int)$empresa->id_tipo_empresa === 1) {
            // Buscamos al menos una licencia activa (id_estado = 1) y vigente
            $licenciaActiva = \Illuminate\Support\Facades\DB::table('licencias')
                ->where('NIT', $user->NIT)
                ->where('id_estado', 1) // Activa/Vigente
                ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
                ->exists();

            if (!$licenciaActiva) {
                // Evitar bucle infinito
                if ($request->routeIs('error.licencia-vencida') || $request->routeIs('error.no-nit')) {
                    return $next($request);
                }

                return redirect()->route('error.licencia-vencida');
            }
        }

        return $next($request);
    }
}
