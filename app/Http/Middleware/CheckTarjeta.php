<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TitularidadTarjeta;

class CheckTarjeta
{
    /**
     * Verifica que el pasajero tenga una titularidad de tarjeta activa.
     * Si no la tiene, lo redirige a la vista de onboarding.
     *
     * La ruta 'pasajero.tarjeta.sin-tarjeta' está excluida del check
     * para evitar bucles infinitos.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Rutas que no requieren tarjeta (onboarding)
        $excluidas = [
            'pasajero.tarjeta.sin-tarjeta', // Asegúrate que este nombre sea exacto al de web.php
            'pasajero.tarjeta.registrar',
            'pasajero.tarjeta.comprar',
            'logout',
        ];

        // Prueba usando named routes de forma más robusta:
        if ($request->route() && in_array($request->route()->getName(), $excluidas)) {
            return $next($request);
        }

        $tieneTarjeta = TitularidadTarjeta::where('doc_usuario', $user->doc_usuario)
            ->where('id_estado', 1)
            ->exists();

        if (! $tieneTarjeta) {
            return redirect()->route('pasajero.tarjeta.sin-tarjeta');
        }

        return $next($request);
    }
}
