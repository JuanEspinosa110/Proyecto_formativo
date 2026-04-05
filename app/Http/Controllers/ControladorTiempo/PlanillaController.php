<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Viaje;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanillaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $nit = $user->getActiveNit();

        // 1. Obtener listas para los filtros
        $busesList = Bus::where('NIT', $nit)->orderBy('placa')->get();
        $conductoresList = \App\Models\Usuario::where('NIT', $nit)
            ->where('id_tipo_usuario', 3) // Conductores
            ->orderBy('primer_nombre')
            ->get();

        // Rutas que tienen viajes en esta empresa
        $rutasList = \App\Models\Ruta::whereHas('viajes', function ($q) use ($nit) {
            $q->whereHas('bus', fn($sq) => $sq->where('NIT', $nit));
        })->get();

        // 2. Query con filtros
        $query = Viaje::with([
            'bus',
            'conductor',
            'ruta.barrioOrigen',
            'ruta.barrioDestino',
            'recorridos.novedades'
        ])
            ->whereHas('bus', function ($q) use ($nit) {
                $q->where('NIT', $nit);
            });

        if ($request->filled('placa')) {
            $query->where('placa', $request->placa);
        }
        if ($request->filled('doc_usuario')) {
            $query->where('doc_us', $request->doc_usuario);
        }
        if ($request->filled('id_ruta')) {
            $query->where('id_ruta', $request->id_ruta);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $planilla = $query->orderBy('id_viaje', 'desc')->paginate(20)->withQueryString();

        // Novedades: Para controladores de tiempo, los buses fuera de servicio (No operables)
        $novedades = Bus::with(['estado'])
            ->where('NIT', $nit)
            ->get()
            ->filter(fn($b) => !$b->isOperable());

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
        $user = Auth::user();
        $nit = $user->getActiveNit();

        $asig = Viaje::with([
            'bus',
            'conductor',
            'ruta.barrioOrigen',
            'ruta.barrioDestino'
        ])
            ->whereHas('bus', fn($q) => $q->where('NIT', $nit))
            ->findOrFail($id);

        $query = \App\Models\Recorrido::with('novedades')
            ->where('id_viaje', $asig->id_viaje);

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
