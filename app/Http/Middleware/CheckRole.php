<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si el rol del usuario está en los permitidos, continuar
        if (in_array($user->id_tipo_usuario, $roles)) {
            return $next($request);
        }

        // Redirección por seguridad si intenta entrar a donde no debe
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
