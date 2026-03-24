<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Mapa de nombre de rol → id_tipo_usuario en tabla tipo_usuario
    private const ROLES = [
        'admin' => 1,
        'pasajero' => 2,
        'conductor' => 3,
        'auxiliar_empresa' => 4,
        'propietario' => 5,
        'setp' => 6,
        'coordinador_bus' => 7,
        'ganagana' => 8,
        'jefe_mantenimiento' => 9,
    ];

    /**
     * Permite usar tanto ids como nombres de rol en el middleware: role:admin o role:1
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para continuar.');
        }

        $user = auth()->user();
        $userRoleId = (int) $user->id_tipo_usuario;

        // Convertir nombres de rol a id
        $roleIds = array_map(function($role) {
            if (is_numeric($role)) {
                return (int)$role;
            }
            return self::ROLES[$role] ?? null;
        }, $roles);

        // Si el rol del usuario está en los permitidos, continuar
        if (in_array($userRoleId, $roleIds, true)) {
            return $next($request);
        }

        // Redirección personalizada según el tipo de usuario
        switch ($userRoleId) {
            case self::ROLES['auxiliar_empresa']:
                return redirect()->route('empresa.dashboard')
                    ->with('error', 'Acceso restringido: Solo puedes acceder a tu panel de auxiliar.');
            case self::ROLES['propietario']:
                return redirect()->route('propietario.dashboard')
                    ->with('error', 'Acceso restringido: Solo puedes acceder a tu panel de propietario.');
            case self::ROLES['admin']:
                return redirect()->route('admin.dashboard')
                    ->with('error', 'No tienes permisos para esta área.');
            default:
                return redirect('/')
                    ->with('error', 'Acceso no autorizado.');
        }
    }
}
