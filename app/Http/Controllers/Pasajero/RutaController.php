<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Barrio;
use App\Models\Empresa;
use App\Models\Asignacion;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index(Request $request)
    {
        $ciudad = auth()->user()->id_ciudad;

        $query = Ruta::with(['barrioOrigen', 'barrioDestino', 'asignaciones' => function ($q) {
                        $q->where('id_estado', 1)->with('empresa');
                    }])
                    ->where('id_ciudad', $ciudad)
                    ->where('id_estado', 1); // Solo rutas activas/habilitadas

        if ($request->filled('codigo')) {
            $query->where('codigo_ruta', $request->codigo);
        }

        if ($request->filled('barrio_origen')) {
            $query->where('id_barrio_origen', $request->barrio_origen);
        }

        if ($request->filled('barrio_destino')) {
            $query->where('id_barrio_destino', $request->barrio_destino);
        }

        $rutas = $query->orderBy('codigo_ruta')->paginate(12);

        // Barrios de la ciudad para los selects de filtro
        $barrios = Barrio::where('id_ciudad', $ciudad)->orderBy('nombre')->get();

        return view('pasajero.rutas.index', compact('rutas', 'barrios'));
    }
}
