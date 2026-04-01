<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Asignacion;
use App\Models\Ruta;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Buses activos de la empresa del controlador
        $totalBuses     = Bus::where('NIT', $user->NIT)->count();
        
        // Buses que tienen viajes programados o en curso para HOY
        $busesEnRuta = Bus::where('NIT', $user->NIT)
            ->whereHas('viajes', function($q) {
                $q->whereDate('fecha', \Carbon\Carbon::today());
            })
            ->count();

        $busesInactivos = Bus::where('NIT', $user->NIT)
            ->whereDoesntHave('viajes', function($q) {
                $q->whereDate('fecha', \Carbon\Carbon::today());
            })
            ->count();

        $asignaciones = \App\Models\Viaje::with(['bus', 'conductor', 'ruta.barrioOrigen', 'ruta.barrioDestino'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->orderBy('id_viaje', 'desc')
            ->take(10)
            ->get();

        // Rutas de la empresa (basadas en buses asignados)
        $rutasActivas = Ruta::whereHas('asignaciones.bus', fn($q) => $q->where('NIT', $user->NIT))
            ->with(['barrioOrigen', 'barrioDestino'])
            ->count();

        // ─── LÓGICA DE FRECUENCIAS ───
        $rutasDetalle = Ruta::whereHas('asignaciones.bus', fn($q) => $q->where('NIT', $user->NIT))
            ->with(['barrioOrigen', 'barrioDestino'])
            ->get();

        foreach ($rutasDetalle as $ruta) {
            $ultimoRecorrido = \App\Models\Recorrido::with('viaje')
                ->whereHas('viaje', function($q) use ($ruta) {
                    $q->where('id_ruta', $ruta->id_ruta);
                })
                ->whereDate('hora_salida', \Carbon\Carbon::today())
                ->orderBy('hora_salida', 'desc')
                ->first();

            if ($ultimoRecorrido) {
                $ruta->hora_salida = \Carbon\Carbon::parse($ultimoRecorrido->hora_salida);
                $ruta->ultimo_bus = $ultimoRecorrido->viaje->placa ?? null;
            } else {
                $ruta->hora_salida = null;
                $ruta->ultimo_bus = null;
            }
        }

        return view('controlador-tiempo.dashboard', compact(
            'totalBuses',
            'busesEnRuta',
            'busesInactivos',
            'asignaciones',
            'rutasActivas',
            'rutasDetalle'
        ));
    }
}
