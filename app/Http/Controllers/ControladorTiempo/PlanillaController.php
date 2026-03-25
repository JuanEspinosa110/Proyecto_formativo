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
                'ruta.barrioDestino',
                'recorridos.viaje',
                'recorridos.novedades'
            ])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->orderBy('id_asignacion', 'desc')
            ->paginate(20);

        // Filtrar estrictamente los recorridos en memoria para que solo pertenezcan
        // al conductor que está asignado, impidiendo que choferes vean turnos ajenos del mismo bus.
        $planilla->getCollection()->transform(function ($asig) {
            if ($asig->relationLoaded('recorridos')) {
                $asig->setRelation('recorridos', $asig->recorridos->filter(function ($rec) use ($asig) {
                    return $rec->viaje && $rec->viaje->doc_us == $asig->doc_usuario;
                })->values());
            }
            return $asig;
        });

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
