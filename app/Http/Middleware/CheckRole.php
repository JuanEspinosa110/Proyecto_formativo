<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    // Mapa de nombre de rol → id_tipo_usuario en tabla tipo_usuario
    private const ROLES = [
        'admin'              => 1,
        'pasajero'           => 2,
        'conductor'          => 3,
        'gestor_setp'        => 6,
        'jefe_mantenimiento' => 7,
        'controlador_tiempo' => 8,
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Convertimos los nombres de roles (si vienen como strings) a sus IDs correspondientes
        $roleIds = array_map(function($role) {
            return is_numeric($role) ? (int)$role : (self::ROLES[$role] ?? $role);
        }, $roles);

        // Si el rol del usuario está en los permitidos, continuar
        if (in_array($user->id_tipo_usuario, $roleIds)) {
            return $next($request);
        }

        // Redirección por seguridad si intenta entrar a donde no debe (lógica de origin/feature/conductor)
        if ($user->id_tipo_usuario == 6 || $user->id_tipo_usuario == 9) {
            return redirect()->route('propietario.dashboard')
                ->with('error', 'Acceso restringido: Solo puedes acceder a tu panel de propietario.');
        }

        if ($user->id_tipo_usuario == 1) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos para esta área.');
        }

        return redirect('/')->with('error', 'Acceso no autorizado.');
    }
}
