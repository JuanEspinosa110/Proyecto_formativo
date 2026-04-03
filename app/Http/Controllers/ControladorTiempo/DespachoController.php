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

        // Query principal basado en VIAJES (Turnos diarios)
        $query = \App\Models\Viaje::with(['bus', 'conductor', 'ruta.barrioOrigen', 'ruta.barrioDestino', 'recorridos'])
            ->whereHas('bus', fn($q) => $q->where('NIT', $user->NIT));

        // Filtro por fecha (Por defecto HOY)
        $fechaFiltro = $request->input('fecha', \Carbon\Carbon::today()->toDateString());
        $query->whereDate('fecha', $fechaFiltro);

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
            if ($request->estado == 'finalizado') {
                $query->where('id_estado', 5);
            } elseif ($request->estado == 'iniciado') {
                $query->whereHas('recorridos');
            } elseif ($request->estado == 'pendiente') {
                $query->where('id_estado', 1)->whereDoesntHave('recorridos');
            }
        }

        // Ordenamiento personalizado:
        // 1 = Activo CON recorridos (En curso)
        // 2 = Activo SIN recorridos (Pendiente)
        // 3 = Finalizado
        $despachos = $query->orderByRaw("
            CASE 
                WHEN id_estado != 5 AND EXISTS (SELECT 1 FROM recorridos WHERE recorridos.id_viaje = viaje.id_viaje) THEN 1
                WHEN id_estado != 5 THEN 2
                ELSE 3
            END ASC
        ")->orderBy('fecha', 'asc')->paginate(15)->withQueryString();
            
        // Buses que ya iniciaron recorrido hoy
        $busesIniciados = \App\Models\Viaje::whereHas('bus', fn($q) => $q->where('NIT', $user->NIT))
            ->whereDate('fecha', \Carbon\Carbon::today())
            ->whereHas('recorridos')
            ->count();

        // Rutas disponibles para la empresa
        $rutas = Ruta::whereHas('asignaciones.bus', fn($q) => $q->where('NIT', $user->NIT))
            ->with(['barrioOrigen', 'barrioDestino'])
            ->get();

        return view('controlador-tiempo.despacho.index', [
            'asignaciones' => $despachos, // Mantenemos el nombre para compatibilidad parcial si es necesario, o lo cambiamos
            'rutas' => $rutas,
            'busesIniciados' => $busesIniciados,
            'fechaFiltro' => $fechaFiltro
        ]);
    }
}
