<?php

namespace App\Http\Controllers\ControladorTiempo;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Bus;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DespachoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filtros dinámicos
        // Solo asignaciones activas
        $query = Asignacion::with(['bus', 'usuario', 'viajes' => function($q) {
                $q->whereDate('fecha', today());
            }, 'ruta.barrioOrigen', 'ruta.barrioDestino'])
            ->where('id_estado', 1)
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT));

        if ($request->filled('placa')) {
            $query->where('placa', 'LIKE', '%' . $request->placa . '%');
        }

        if ($request->filled('ruta_id')) {
            $query->where('id_ruta', $request->ruta_id);
        }

        if ($request->filled('doc_usuario')) {
            $query->where('doc_usuario', $request->doc_usuario);
        }

        if ($request->filled('estado')) {
            if ($request->estado == 'iniciado') {
                $query->whereHas('viajes', fn($q) => $q->whereDate('fecha', today()));
            } elseif ($request->estado == 'pendiente') {
                $query->whereDoesntHave('viajes', fn($q) => $q->whereDate('fecha', today()));
            }
        }

        $asignaciones = $query->orderBy('id_asignacion', 'desc')->paginate(15)->withQueryString();
            
        // Buses que ya iniciaron turno hoy
        $busesIniciados = Asignacion::whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->where('id_estado', 1)
            ->whereHas('viajes', fn($q) => $q->whereDate('fecha', today()))
            ->count();

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
            'rutas',
            'busesIniciados'
        ));
    }
}
