<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    // Mapa de nombre de rol → id_tipo_usuario en tabla tipo_usuario
    private const ROLES = [
        'admin'        => 1,
        'pasajero'     => 2,
        'conductor'    => 3,
        'gestor_setp'  => 11,
    ];

    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para continuar.');
        }

        $idEsperado = self::ROLES[$role] ?? null;

        if ($idEsperado === null || (int) $user->id_tipo_usuario !== $idEsperado) {
            abort(403, 'No tiene permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
