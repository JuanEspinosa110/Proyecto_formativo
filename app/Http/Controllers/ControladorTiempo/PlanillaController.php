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
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Obtener listas para los filtros
        $busesList = Bus::where('NIT', $user->NIT)->orderBy('placa')->get();
        $conductoresList = \App\Models\Usuario::where('NIT', $user->NIT)
            ->where('id_tipo_usuario', 3) // Conductores
            ->orderBy('primer_nombre')
            ->get();
        
        // Rutas que tienen asignaciones en esta empresa
        $rutasList = \App\Models\Ruta::whereHas('asignaciones', function($q) use ($user) {
            $q->where('Nit', $user->NIT);
        })->get();

        // 2. Query con filtros
        $query = Asignacion::with([
                'bus', 
                'usuario', 
                'ruta.barrioOrigen', 
                'ruta.barrioDestino',
                'recorridos.novedades'
            ])
            ->where('Nit', $user->NIT);

        if ($request->filled('placa')) {
            $query->where('placa', $request->placa);
        }
        if ($request->filled('doc_usuario')) {
            $query->where('doc_usuario', $request->doc_usuario);
        }
        if ($request->filled('id_ruta')) {
            $query->where('id_ruta', $request->id_ruta);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_inicio', $request->fecha);
        }

        $planilla = $query->orderBy('id_asignacion', 'desc')->paginate(20)->withQueryString();

        // Novedades: buses en taller (mantenimiento) o inactivos como referencia
        $novedades = Bus::with(['estado'])
            ->where('NIT', $user->NIT)
            ->where('id_estado', '!=', 1)
            ->get();

        return view('controlador-tiempo.planillas.index', compact(
            'planilla', 
            'novedades', 
            'busesList', 
            'conductoresList', 
            'rutasList'
        ));
    }

    public function show(Request $request, $id)
    {
        $asig = Asignacion::with([
            'bus', 
            'usuario', 
            'ruta.barrioOrigen', 
            'ruta.barrioDestino'
        ])->findOrFail($id);

        $query = \App\Models\Recorrido::with('novedades')
            ->whereHas('viaje', function($q) use ($asig) {
                $q->where('placa', $asig->placa)
                  ->where('doc_us', $asig->doc_usuario);
            });

        if ($request->filled('fecha')) {
            $query->whereDate('hora_salida', $request->fecha);
        }

        $recorridos = $query->orderBy('hora_salida', 'desc')
            ->paginate(3)
            ->withQueryString();

        return view('controlador-tiempo.planillas.show', compact('asig', 'recorridos'));
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
