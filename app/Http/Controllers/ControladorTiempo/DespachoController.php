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
        $query = Asignacion::with(['bus', 'usuario', 'recorridos', 'ruta.barrioOrigen', 'ruta.barrioDestino'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT));

        if ($request->filled('placa')) {
            $query->where('placa', 'LIKE', '%' . $request->placa . '%');
        }

        if ($request->filled('ruta_id')) {
            $query->where('id_ruta', $request->ruta_id);
        }

        if ($request->filled('doc_usuario')) {
            $query->where('doc_us', $request->doc_usuario);
        }

        if ($request->filled('estado')) {
            if ($request->estado == 'iniciado') {
                $query->whereHas('recorridos');
            } elseif ($request->estado == 'pendiente') {
                $query->whereDoesntHave('recorridos');
            }
        }

        $asignaciones = $query->orderBy('id_asignacion', 'desc')->paginate(15)->withQueryString();
            
        // Buses que ya iniciaron su recorrido (tienen al menos un registro en la tabla recorridos)
        $busesIniciados = Asignacion::whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->whereHas('recorridos')
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
