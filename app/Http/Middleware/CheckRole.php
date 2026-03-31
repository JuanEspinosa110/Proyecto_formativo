<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    // Mapa de nombre de rol → id_tipo_usuario en tabla tipo_usuario
    private const ROLES = [
        'admin' => 1,
        'pasajero' => 2,
        'conductor' => 3,
        'auxiliar' => 4,
        'propietario' => 5,
        'gestor_setp' => 6,
        'coordinador_bus' => 7,
        'ganagana' => 8, // Alias heredado — ahora es GESTOR DE RECARGAS
        'gestor_recargas' => 8,
        'jefe_mantenimiento' => 9,
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para continuar.');
        }

        $user = auth()->user();

        // Verificar si el usuario tiene al menos uno de los roles permitidos (incluyendo herencia)
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Redirección por seguridad si intenta entrar a donde no debe
        if ($user->id_tipo_usuario == 5) {
            return redirect()->route('propietario.dashboard')
                ->with('error', 'Acceso restringido: Solo puedes acceder a tu panel de propietario.');
        }

        if ($user->id_tipo_usuario == 9) {
            return redirect()->route('jefemantenimiento.dashboard')
                ->with('error', 'Acceso restringido: Solo puedes acceder a tu panel de jefe de mantenimiento.');
        }

        if ($user->id_tipo_usuario == 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos para esta área.');
        }

        if ($user->id_tipo_usuario == 4) {
            return redirect()->route('empresa.dashboard')
                ->with('error', 'Acceso restringido.');
        }

        if ($user->id_tipo_usuario == 8) {
            return redirect()->route('gestor-recargas.recargar');
        }
        return redirect('/')->with('error', 'Acceso no autorizado.');

    }
}
