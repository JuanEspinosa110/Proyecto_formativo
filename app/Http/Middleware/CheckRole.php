<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

<<<<<<< HEAD

=======
>>>>>>> develop
class CheckRole
{
    // Mapa de nombre de rol → id_tipo_usuario en tabla tipo_usuario
    private const ROLES = [
        'admin' => 1,
        'pasajero' => 2,
        'conductor' => 3,
<<<<<<< HEAD
        'auxiliar' => 4,
        'propietario' => 5,
        'gestor_setp' => 6,
        'coordinador_bus' => 7,
        'ganagana'           => 8, // Alias heredado — ahora es GESTOR DE RECARGAS
        'gestor_recargas'    => 8,
=======
        'auxiliar_empresa' => 4,
        'propietario' => 5,
        'setp' => 6,
        'coordinador_bus' => 7,
        'ganagana' => 8,
>>>>>>> develop
        'jefe_mantenimiento' => 9,
    ];

    /**
<<<<<<< HEAD
     * Handle an incoming request.
=======
     * Permite usar tanto ids como nombres de rol en el middleware: role:admin o role:1
>>>>>>> develop
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

        // Convertimos los nombres de roles (si vienen como strings) a sus IDs correspondientes
        $roleIds = array_map(function ($role) {
            return is_numeric($role) ? (int)$role : (self::ROLES[$role] ?? $role);
        }, $roles);

        // Si el rol del usuario está en los permitidos, continuar
<<<<<<< HEAD
        if (in_array($user->id_tipo_usuario, $roleIds)) {
            return $next($request);
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

        return redirect('/')->with('error', 'Acceso no autorizado.');
=======
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
>>>>>>> develop
    }
}
