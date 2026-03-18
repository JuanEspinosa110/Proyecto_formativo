<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Bus;
use App\Models\Ruta;
use Illuminate\Support\Facades\Auth;

class DespachoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Asignaciones (bus + conductor + ruta) de la empresa
        $asignaciones = Asignacion::with(['bus', 'usuario', 'ruta.barrioOrigen', 'ruta.barrioDestino'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->orderBy('id_viaje', 'desc')
            ->paginate(15);

        // Buses disponibles (activos, sin asignación activa de conductor)
        $busesDisponibles = Bus::where('NIT', $user->NIT)
            ->where('id_estado', 1)
            ->get();

        // Rutas disponibles
        $rutas = Ruta::with(['barrioOrigen', 'barrioDestino'])
            ->where('id_estado', 1)
            ->get();

        return view('controlador-tiempo.despacho.index', compact(
            'asignaciones',
            'busesDisponibles',
            'rutas'
        ));
    }
}
