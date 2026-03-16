<?php

namespace App\Http\Controllers\Pasajero;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Barrio;
use App\Models\Ciudad;

/**
 * Nota: La BD actual no tiene tabla de coordenadas de paradas.
 * El mapa se construye con los barrios de origen/destino de las rutas
 * de la ciudad del pasajero. Cuando exista la tabla `parada` o similar
 * con latitud/longitud reales, actualizar este controlador.
 */
class MapaController extends Controller
{
    public function index()
    {
        $ciudad = auth()->user()->id_ciudad;

        // Rutas activas de la ciudad con barrios
        $rutas = Ruta::with(['barrioOrigen', 'barrioDestino'])
                     ->where('id_ciudad', $ciudad)
                     ->where('id_estado', 1)
                     ->get();

        // Barrios únicos que aparecen como origen o destino (posibles paradas)
        $barrioIds = $rutas->pluck('id_barrio_origen')
                           ->concat($rutas->pluck('id_barrio_destino'))
                           ->unique()
                           ->values();

        $barrios = Barrio::whereIn('id_barrio', $barrioIds)
                         ->where('id_ciudad', $ciudad)
                         ->orderBy('nombre')
                         ->get();

        // Ciudad actual del pasajero para centrar el mapa
        $ciudadObj = Ciudad::where('id_ciudad', $ciudad)->first();

        // Serializar para JS / Leaflet
        // Los barrios no tienen lat/lng en la BD actual, se usan
        // coordenadas aproximadas del centro de la ciudad como fallback.
        $mapaData = [
            'ciudad'  => $ciudadObj,
            'rutas'   => $rutas,
            'barrios' => $barrios,
        ];

        return view('pasajero.mapa.index', compact('rutas', 'barrios', 'ciudadObj', 'mapaData'));
    }
}
