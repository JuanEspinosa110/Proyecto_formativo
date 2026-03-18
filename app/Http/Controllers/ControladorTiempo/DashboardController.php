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
        $busesEnRuta    = Bus::where('NIT', $user->NIT)->where('id_estado', 1)->count();
        $busesInactivos = Bus::where('NIT', $user->NIT)->where('id_estado', '!=', 1)->count();

        // Asignaciones vigentes de la empresa
        $asignaciones = Asignacion::with(['bus', 'usuario', 'ruta'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->orderBy('id_viaje', 'desc')
            ->take(10)
            ->get();

        // Rutas de la empresa (basadas en buses asignados)
        $rutasActivas = Ruta::whereHas('asignaciones.bus', fn($q) => $q->where('NIT', $user->NIT))
            ->with(['barrioOrigen', 'barrioDestino'])
            ->count();

        return view('controlador-tiempo.dashboard', compact(
            'totalBuses',
            'busesEnRuta',
            'busesInactivos',
            'asignaciones',
            'rutasActivas'
        ));
    }
}
