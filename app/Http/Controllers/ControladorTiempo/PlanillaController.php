<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanillaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Asignaciones del día con sus respectivos recorridos y novedades
        $planilla = Asignacion::with([
                'bus', 
                'usuario', 
                'ruta.barrioOrigen', 
                'ruta.barrioDestino',
                'recorridos.novedades'
            ])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->orderBy('id_viaje', 'desc')
            ->paginate(20);

        // Novedades: buses en taller (mantenimiento) o inactivos como referencia
        $novedades = Bus::with(['estado'])
            ->where('NIT', $user->NIT)
            ->where('id_estado', '!=', 1)
            ->get();

        return view('controlador-tiempo.planillas.index', compact('planilla', 'novedades'));
    }

    public function store(Request $request)
    {
        // Punto de extensión: aquí iría la lógica para guardar una planilla personalizada
        return back()->with('success', 'Planilla registrada correctamente.');
    }

    public function registrarNovedad(Request $request, $id)
    {
        // Punto de extensión: aquí iría la lógica de novedades por turno
        return back()->with('success', 'Novedad registrada correctamente.');
    }
}
